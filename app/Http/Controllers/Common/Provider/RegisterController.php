<?php

namespace App\Http\Controllers\Common\Provider;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProviderSignUpRequest;
use App\Models\Common\AuthLog;
use App\Models\Common\Provider;
use App\Models\Common\Setting;
use App\Services\ReferralResource;
use App\Traits\Encryptable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    use Encryptable;

    public function signup(ProviderSignUpRequest $request)
    {
        // Normalize the email in the request
        $this->normalizeEmail($request);

        // Encrypt sensitive data in the request
        $this->encryptSensitiveData($request);

        // Decode the company id from the request's salt key
        $company_id = base64_decode($request->salt_key);

        // Get the settings related to the request
        $settings = $this->getSettings($company_id);

        $email = $request->email;
        $mobile = $request->mobile;
        $country_code = $request->country_code;

        $this->validate(
            $request,
            [
                "email" => [
                    Rule::unique("providers")->where(function ($query) use ($email, $company_id) {
                        return $query
                            ->where("email", $email)
                            ->where("company_id", $company_id);
                    }),
                ],
                "mobile" => [
                    Rule::unique("providers")->where(function ($query) use ($mobile, $company_id, $country_code) {
                        return $query
                            ->where("mobile", $mobile)
                            ->where("country_code", $country_code)
                            ->where("company_id", $company_id);
                    }),
                ],
            ],
            [
                "email.unique" =>
                "User already registered with given email-Id!",
                "mobile.unique" =>
                "User already registered with given mobile number!",
            ]
        );

        $siteConfig = $settings->site;

        // Validate the referral code in the request
        $this->validateReferralCode($request, $company_id);

        // Generate a unique referral id for the company
        $referral_unique_id = $this->generateReferralCode($company_id);

        // Encrypt sensitive data in the request
        $this->decryptSensitiveData($request);

        // Create or update the user
        $provider = $this->createProvider(
            $request,
            $referral_unique_id,
            $company_id
        );

        // Create a log for the authentication
        $this->handleProviderPicture($request, $provider, $company_id);

        // Create a log for the authentication
        $this->createAuthLog($request, $provider);

        // Add the company id to the request and remove the salt key
        $request->request->add([
            "company_id" => $company_id,
        ]);
        $request->request->remove("salt_key");

        // Encrypt the email in the request
        $request->merge([
            "email" => $this->customEncrypt($request->email, env("DB_SECRET")),
        ]);

        // Prepare the credentials for authentication
        $credentials = [
            "email" => $request->email,
            "password" =>
            $request->social_unique_id != null
                ? $request->social_unique_id
                : $request->password,
            "company_id" => $provider->company_id,
        ];

        // Attempt to authenticate the user
        $token = Auth::guard("provider")->attempt($credentials);

        // Check if the site has referrals enabled and if the request has a referral code
        if (!empty($siteConfig->send_email) && $siteConfig->send_email == 1) {
            // send welcome email here
            Helper::siteRegisterMail($provider);
        }

        //check user referrals
        if (!empty($siteConfig->referral) && $siteConfig->referral == 1 && $request->referral_code) {
            //call referral function
            (new ReferralResource())->create_referral(
                $request->referral_code,
                $provider,
                $settings,
                "provider"
            );
        }

        // Find the newly created or updated provider
        $newUser = Provider::find($provider->id);

        // Return the response with the token and the provider
        return Helper::getResponse([
            "data" => [
                "token_type" => "Bearer",
                "expires_in" => config("jwt.ttl", "0") * 60,
                "access_token" => $token,
                "user" => $newUser,
            ],
        ]);
    }

    private function normalizeEmail(ProviderSignUpRequest $request): void
    {
        if ($request->has("email")) {
            $request->merge(["email" => strtolower($request->email)]);
        }
    }

    private function encryptSensitiveData(ProviderSignUpRequest $request): void
    {
        $request->merge([
            "email" => $this->customEncrypt($request->email, env("DB_SECRET")),
            "mobile" => $this->customEncrypt($request->mobile, env("DB_SECRET")),
        ]);
    }

    private function decryptSensitiveData(ProviderSignUpRequest $request): void
    {
        $request->merge([
            "email" => $this->customDecrypt($request->email, env("DB_SECRET")),
            "mobile" => $this->customDecrypt($request->mobile, env("DB_SECRET")),
        ]);
    }

    private function getSettings($company_id)
    {
        return Helper::jsonEncodeDecode(
            Setting::where(
                "company_id",
                $company_id
            )->first()->settings_data
        );
    }

    /**
     * @throws ValidationException
     */
    private function validateReferralCode(ProviderSignUpRequest $request, $company_id): void
    {
        if ($request->has("referral_code") && $request->referral_code != "") {
            $validate["referral_unique_id"] = $request->referral_code;
            $validate["company_id"] = $company_id;
            $validator = (new ReferralResource())->checkReferralCode($validate);
            if (!$validator->fails()) {
                $validator
                    ->errors()
                    ->add("referral_code", "Invalid Referral Code");
                throw new ValidationException($validator);
            }
        }
    }

    private function generateReferralCode($company_id): string
    {
        return (new ReferralResource())->generateCode($company_id);
    }

    private function createAuthLog(ProviderSignUpRequest $request, $provider): void
    {
        AuthLog::create([
            "user_type" => "Provider",
            "user_id" => $provider->id,
            "type" => "login",
            "data" => json_encode([
                "data" => [
                    $request->getMethod() =>
                    $request->getPathInfo() .
                        " " .
                        $request->getProtocolVersion(),
                    "host" => $request->getHost(),
                    "ip" => $request->getClientIp(),
                    "user_agent" => $request->userAgent(),
                    "date" => Carbon::now()->format("Y-m-d H:i:s"),
                ],
            ]),
        ]);
    }

    private function createProvider(
        $request,
        $referral_unique_id,
        $company_id
    ): Provider {
        $provider = new Provider();
        $provider->first_name = $request->first_name;
        $provider->last_name = $request->last_name;
        $provider->email = $request->email;
        $provider->gender = $request->gender;
        $provider->country_code = $request->country_code;
        $provider->mobile = $request->mobile;
        $provider->password =
            $request->social_unique_id != null
            ? Hash::make($request->social_unique_id)
            : Hash::make($request->password);
        $provider->referral_unique_id = $referral_unique_id;
        $provider->company_id = $company_id;
        $provider->social_unique_id = $request->social_unique_id;
        $provider->device_type = $request->device_type;
        $provider->device_token = $request->device_token;
        $provider->social_unique_id =
            $request->social_unique_id != null
            ? $request->social_unique_id
            : null;
        $provider->login_by =
            $request->login_by != null ? $request->login_by : "MANUAL";
        $provider->country_id = $request->country_id;
        $provider->zipcode = $request->zipcode;
        $provider->suite = $request->suite;
        $provider->state = $request->state_id;
        $provider->address = $request->address;
        $provider->save();

        return $provider;
    }

    private function handleProviderPicture($request, $provider, $company_id): void
    {

        if ($request->hasFile("picture")) {
            $provider->picture = Helper::uploadFile(
                $request->file("picture"),
                "provider/profile",
                $provider->id .
                    "." .
                    $request->file("picture")->getClientOriginalExtension(),
                $company_id
            );
        }
        $provider->qrcode_url = Helper::qrCode(
            json_encode([
                "country_code" => $request->country_code,
                "phone_number" => $request->mobile,
            ]),
            $provider->id . ".png",
            $company_id
        );
        $provider->save();
    }
}

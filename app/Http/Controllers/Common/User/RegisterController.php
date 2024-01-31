<?php

namespace App\Http\Controllers\Common\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserSignUpRequest;
use App\Models\Common\AuthLog;
use App\Models\Common\CompanyCity;
use App\Models\Common\CompanyCountry;
use App\Models\Common\Setting;
use App\Models\Common\User;
use App\Services\ReferralResource;
use App\Traits\Encryptable;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    use Encryptable;

    const EMAIL_EXIST_ERROR = "User already registered with given email-Id!";
    const MOBILE_EXIST_ERROR = "User already registered with given mobile number!";
    const DATE_FORMAT = "Y-m-d H:i:s";

    /**
     * @throws ValidationException
     */
    public function signup(UserSignUpRequest $request): JsonResponse
    {
        // Normalize the email in the request
        $this->normalizeEmail($request);

        // Get the settings related to the request
        $settings = $this->getSettings($request);

        // Extract the site configuration from the settings
        $siteConfig = $settings->site;

        // Decode the company id from the request's salt key
        $company_id = base64_decode($request->salt_key);

        // Validate the referral code in the request
        $this->validateReferralCode($request);

        // Generate a unique referral id for the company
        $referral_unique_id = $this->generateReferralCode($company_id);

        // Encrypt sensitive data in the request
        $this->encryptSensitiveData($request);
        // Create a new validator instance
        $validator = Validator::make([], [], []);
        $currentUser = null;

        // Check if a user with the same email and user type 'INSTANT' already exists
        $registeredEmail = User::where("email", $request->email)
            ->where("user_type", "INSTANT")
            ->first();

        // Check if a user with the same mobile number and user type 'INSTANT' already exists
        $registeredMobile = User::where("country_code", $request->country_code)
            ->where("mobile", $request->mobile)
            ->where("user_type", "INSTANT")
            ->first();

        // If both email and mobile number are already registered
        if ($registeredEmail != null && $registeredMobile != null) {
            // Add an error to the validator and throw a ValidationException
            $validator->errors()->add("email", self::EMAIL_EXIST_ERROR);
            throw new ValidationException($validator);
        } elseif ($registeredMobile != null) {
            // If only the mobile number is already registered,
            // add an error to the validator and throw a ValidationException
            $validator->errors()->add("mobile", self::MOBILE_EXIST_ERROR);
            throw new ValidationException($validator);
        } elseif ($registeredEmail != null) {
            // If only the email is already registered, set the current user to the registered email user
            $currentUser = $registeredEmail;
        }

        // Check if a user with the same email and user type 'NORMAL' already exists
        $registeredEmailNormal = User::where("email", $request->email)
            ->where("user_type", "NORMAL")
            ->first();
        if ($registeredEmailNormal != null) {
            // If the email is already registered, add an error to the validator and throw a ValidationException
            $validator->errors()->add("email", self::EMAIL_EXIST_ERROR);
            throw new ValidationException($validator);
        }

        // Check if a user with the same mobile number and user type 'NORMAL' already exists
        $registeredMobileNormal = User::where(
            "country_code",
            $request->country_code
        )
            ->where("mobile", $request->mobile)
            ->where("user_type", "NORMAL")
            ->first();
        if ($registeredMobileNormal != null) {
            // If the mobile number is already registered, add an error to the validator and throw a ValidationException
            $validator->errors()->add("mobile", self::MOBILE_EXIST_ERROR);
            throw new ValidationException($validator);
        }

        // Decrypt the email and encrypt the mobile number in the request
        $request->merge([
            "email" => $this->customDecrypt($request->email, env("DB_SECRET")),
            "mobile" =>
                $request->has("mobile") && $request->mobile
                    ? $this->customEncrypt($request->mobile, env("DB_SECRET"))
                    : null,
        ]);

        // Check if the city exists in the company
        $city = CompanyCity::where("city_id", $request->city_id)->first();
        if ($city == null) {
            $validator->errors()->add("city", "City does not exist!");
            throw new ValidationException($validator);
        }

        // Check if the country exists in the company
        $country = CompanyCountry::where("company_id", $company_id)
            ->where("country_id", $request->country_id)
            ->first();

        // Create or update the user
        $user = $this->createOrUpdateUser(
            $request,
            $currentUser,
            $referral_unique_id,
            $country,
            $city
        );

        // Handle the user's profile picture
        $this->handleUserPicture($request, $user);

        // Create a log for the authentication
        $this->createAuthLog($request, $user);

        // Add the company id to the request and remove the salt key
        $request->request->add([
            "company_id" => base64_decode($request->salt_key),
        ]);
        $request->request->remove("salt_key");

        // Encrypt the email in the request
        $request->merge([
            "email" => $this->customEncrypt($request->email, env("DB_SECRET")),
        ]);

        // Prepare the credentials for authentication
        $credentials = [
            "email" => $this->customEncrypt($user->email, env("DB_SECRET")),
            "password" =>
                $request->social_unique_id != null
                    ? $request->social_unique_id
                    : $request->password,
            "company_id" => $user->company_id,
        ];

        // Attempt to authenticate the user
        $token = Auth::guard("user")->attempt($credentials);

        // Check if the site has referrals enabled and if the request has a referral code
        if (
            !empty($siteConfig->referral) &&
            $siteConfig->referral == 1 &&
            $request->referral_code
        ) {
            // Create a referral
            (new ReferralResource())->create_referral(
                $request->referral_code,
                $user,
                $settings,
                "user"
            );
        }

        // Find the newly created or updated user
        $newUser = User::find($user->id);

        // Return the response with the token and the user
        return Helper::getResponse([
            "data" => [
                "token_type" => "Bearer",
                "expires_in" => config("jwt.ttl", "0") * 60,
                "access_token" => $token,
                "user" => $newUser,
            ],
        ]);
    }

    private function normalizeEmail(UserSignUpRequest $request): void
    {
        if ($request->has("email")) {
            $request->merge(["email" => strtolower($request->email)]);
        }
    }

    private function getSettings(UserSignUpRequest $request)
    {
        return Helper::jsonEncodeDecode(
            Setting::where(
                "company_id",
                base64_decode($request->salt_key)
            )->first()->settings_data
        );
    }

    /**
     * @throws ValidationException
     */
    private function validateReferralCode(UserSignUpRequest $request): void
    {
        $company_id = base64_decode($request->salt_key);

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

    private function encryptSensitiveData(UserSignUpRequest $request): void
    {
        $request->merge([
            "email" => $this->customEncrypt($request->email, env("DB_SECRET")),
            "mobile" => $request->has("mobile")
                ? $this->customEncrypt($request->mobile, env("DB_SECRET"))
                : null,
        ]);
    }

    private function createOrUpdateUser(
        $request,
        $currentUser,
        $referral_unique_id,
        $country,
        $city
    ): User
    {
        if ($currentUser == null) {
            $user = new User();
        } else {
            $user = $currentUser;
        }

        $user->fill([
            "first_name" => $request->first_name,
            "last_name" => $request->last_name,
            "email" => $request->email,
            "gender" => $request->gender,
            "country_code" => $request->country_code,
            "mobile" => $request->mobile,
            "password" =>
                $request->social_unique_id != null
                    ? Hash::make($request->social_unique_id)
                    : Hash::make($request->password),
            "payment_mode" => "CASH",
            "user_type" => "NORMAL",
            "referral_unique_id" => $referral_unique_id,
            "company_id" => base64_decode($request->salt_key),
            "device_type" => $request->device_type,
            "device_token" => $request->device_token,
            "social_unique_id" =>
                $request->social_unique_id != null
                    ? $request->social_unique_id
                    : null,
            "login_by" =>
                $request->login_by != null ? $request->login_by : "MANUAL",
            "currency_symbol" => $country->currency,
            "country_id" => $request->country_id,
            "state_id" => $city->state_id,
            "city_id" => $request->city_id,
            "suite" => $request->suite,
        ]);

        $user->save();

        return $user;
    }

    private function handleUserPicture($request, $user): void
    {

        if ($request->hasFile("picture")) {
            $filename = $user->id . "." . $request->file("picture")->getClientOriginalExtension();
            $user->picture = Helper::uploadFile(
                $request->file("picture"),
                "user/profile",
                $filename,
                base64_decode($request->salt_key)
            );
        }

        $user->qrcode_url = Helper::qrCode(
            json_encode([
                "country_code" => $request->country_code,
                "phone_number" => $request->mobile,
            ]),
            $user->id . ".png",
            base64_decode($request->salt_key)
        );

        $user->save();
    }

    private function createAuthLog(UserSignUpRequest $request, $user): void
    {
        AuthLog::create([
            "user_type" => "User",
            "user_id" => $user->id,
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
                    "date" => Carbon::now()->format(self::DATE_FORMAT),
                ],
            ]),
        ]);
    }
}

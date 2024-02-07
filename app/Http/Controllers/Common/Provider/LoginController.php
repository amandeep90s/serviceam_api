<?php

namespace App\Http\Controllers\Common\Provider;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProviderLoginRequest;
use App\Models\Common\AuthLog;
use App\Models\Common\Provider;
use App\Traits\Encryptable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class LoginController extends Controller
{
    use Encryptable;

    public function login(ProviderLoginRequest $request)
    {
        // Normalize the email in the request
        $this->normalizeEmail($request);

        // Encrypt the email in the request
        $this->encryptEmail($request);

        // Encrypt the mobile number in the request
        $this->encryptMobile($request);

        // Validate the request
        $this->validateRequest($request);

        // Initialize response variable
        $response = null;

        try {
            // Try to authenticate the user
            $token = $this->authenticateUser($request);

            // If token is not generated, set the response as invalid credentials
            if (!$token) {
                $response = $this->handleException("Invalid Credentials", 422);
            }
        } catch (TokenExpiredException $e) {
            // Handle token expired exception
            $response = $this->handleException("Token Expired");
        } catch (TokenInvalidException $e) {
            // Handle token invalid exception
            $response = $this->handleException("Token Invalid");
        } catch (JWTException $e) {
            // Handle JWT exception
            $response = $this->handleException($e->getMessage());
        }

        // If response is set, return it
        if ($response != null) {
            return $response;
        }

        // Get the provider details
        $provider = Provider::find(Auth::guard("provider")->user()->id);

        // If the provider's account is disabled, return an exception
        if ($provider->activation_status == 0) {
            return $this->handleException("Account Disabled", 422);
        }

        // Update the provider details
        $this->updateProvider($request, $provider);

        // Create an authentication log
        $this->createAuthLog($request);

        // Get the updated provider details
        $newUser = Provider::find($provider->id);

        // Set the JWT token for the user
        $newUser->jwt_token = $token;

        // Save the user details
        $newUser->save();

        // Return the response with the user details and token
        return Helper::getResponse([
            "data" => [
                "token_type" => "Bearer",
                "expires_in" => config("jwt.ttl", "0") * 60,
                "access_token" => $token,
                "user" => $newUser,
            ],
        ]);
    }

    private function normalizeEmail(ProviderLoginRequest $request)
    {
        if ($request->has("email")) {
            $request->merge(["email" => strtolower($request->email)]);
        }
    }

    private function encryptEmail(ProviderLoginRequest $request)
    {
        if ($request->has("email") && $request->email != "") {
            $request->merge([
                "email" => $this->customEncrypt($request->email, config('app.db_secret')),
            ]);
        }
    }

    private function encryptMobile(ProviderLoginRequest $request)
    {
        if ($request->has("mobile")) {
            $request->merge([
                "mobile" => $this->customEncrypt(
                    $request->mobile,
                    config('app.db_secret')
                ),
            ]);
        }
    }

    private function validateRequest(ProviderLoginRequest $request)
    {
        $rules = [];

        if (!$request->has("email") && !$request->has("mobile")) {
            $rules = [
                "email" => "required|email|max:255",
                "mobile" => "required",
                "country_code" => "required",
            ];
        } elseif (!$request->has("mobile")) {
            $rules = [
                "email" => ["required", "max:255", Rule::exists("providers")],
            ];
        } elseif (!$request->has("email")) {
            $rules = [
                "mobile" => ["required", Rule::exists("providers")],
                "country_code" => "required",
            ];
        }

        $this->validate($request, $rules, [
            "mobile.exists" => "Please Enter a Valid Mobile Number",
            "email.exists" => "Please Enter a Valid Email",
        ]);
    }

    private function authenticateUser(ProviderLoginRequest $request)
    {
        $credentials = $request->has("email") && $request->email != ""
            ? $request->only("email", "password", "company_id")
            : $request->only("country_code", "mobile", "password", "company_id");

        return Auth::guard("provider")->attempt($credentials);
    }

    private function handleException($message, $statusCode = 500)
    {
        return Helper::getResponse([
            "status" => $statusCode,
            "message" => $message,
        ]);
    }

    private function updateProvider($request, $provider)
    {
        $provider->device_type = $request->device_type;
        $provider->device_token = $request->device_token;
        $provider->login_by = $request->login_by != null ? $request->login_by : "MANUAL";
        $provider->is_online = 1;
        $provider->save();
    }

    private function createAuthLog($request)
    {
        AuthLog::create([
            "user_type" => "Provider",
            "user_id" => Auth::guard("provider")->id(),
            "type" => "login",
            "data" => json_encode([
                "data" => [
                    $request->getMethod() => $request->getPathInfo() . " " . $request->getProtocolVersion(),
                    "host" => $request->getHost(),
                    "ip" => $request->getClientIp(),
                    "user_agent" => $request->userAgent(),
                    "date" => \Carbon\Carbon::now()->format("Y-m-d H:i:s"),
                ],
            ]),
        ]);
    }
}

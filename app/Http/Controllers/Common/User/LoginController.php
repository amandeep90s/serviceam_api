<?php

namespace App\Http\Controllers\Common\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Models\Common\AuthLog;
use App\Models\Common\User;
use App\Traits\Encryptable;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class LoginController extends Controller
{
    use Encryptable;

    const DATE_FORMAT = "Y-m-d H:i:s";

    /**
     * @throws ValidationException
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        $this->validateLogin($request);

        $token = $this->attemptAuthentication($request);

        if (!$token) {
            return Helper::getResponse([
                "status" => 422,
                "message" => "Invalid Credentials",
            ]);
        }

        $user = $this->updateUser($request, $token);

        $this->logAuthentication($request);

        return Helper::getResponse([
            "data" => [
                "token_type" => "Bearer",
                "expires_in" => config("jwt.ttl", "0") * 60,
                "access_token" => $token,
                "user" => $user,
            ],
        ]);
    }

    /**
     * @throws ValidationException
     */
    private function validateLogin(Request $request): void
    {
        if ($request->has("email")) {
            $request->merge([
                "email" => strtolower($request->email),
            ]);

            $request->merge([
                "email" => $this->customEncrypt(
                    $request->email,
                    config('app.db_secret')
                ),
            ]);
        }

        if ($request->has("mobile")) {
            $request->merge([
                "mobile" => $this->customEncrypt(
                    $request->mobile,
                    config('app.db_secret')
                ),
            ]);
        }

        if (!$request->has("email") && !$request->has("mobile")) {
            $this->validate($request, [
                "email" => "required|email|max:255",
                "mobile" => "required",
                "country_code" => "required",
            ]);
        } elseif (!$request->has("mobile")) {
            $this->validate($request, [
                "email" => ["required", "max:255", Rule::exists("users")],
            ]);
        } elseif (!$request->has("email")) {
            $this->validate(
                $request,
                [
                    "mobile" => ["required", Rule::exists("users")],
                    "country_code" => "required",
                ],
                [
                    "mobile.exists" => "Please Enter a Valid Mobile Number",
                    "email.exists" => "Please Enter a Valid Email",
                ]
            );
        }
    }

    private function attemptAuthentication(Request $request): ?string
    {
        $result = null;

        $request->request->add([
            "company_id" => base64_decode($request->salt_key),
        ]);
        $request->request->remove("salt_key");

        try {
            if ($request->has("email") && $request->email != "") {
                $result = Auth::guard("user")->attempt(
                    $request->only("email", "password", "company_id", "status")
                );
            } else {
                $result = Auth::guard("user")->attempt(
                    $request->only(
                        "country_code",
                        "mobile",
                        "password",
                        "company_id",
                        "status"
                    )
                );
            }
        } catch (TokenExpiredException $e) {
            $result = Helper::getResponse([
                "status" => 500,
                "message" => "Token Expired",
            ]);
        } catch (TokenInvalidException $e) {
            $result = Helper::getResponse([
                "status" => 500,
                "message" => "Token Invalid",
            ]);
        } catch (JWTException $e) {
            $result = Helper::getResponse([
                "status" => 500,
                "message" => $e->getMessage(),
            ]);
        }

        return $result;
    }

    private function updateUser(
        Request $request,
        string  $token
    ): User|JsonResponse
    {
        $user = User::find(Auth::guard("user")->user()->id);
        if ($user->status == 0) {
            return Helper::getResponse([
                "status" => 422,
                "message" => "Account Disabled",
            ]);
        }
        $user->device_type = $request->device_type;
        $user->device_token = $request->device_token;
        $user->login_by =
            $request->login_by != null ? $request->login_by : "MANUAL";
        $user->jwt_token = $token;
        $user->save();

        return $user;
    }

    private function logAuthentication(Request $request): void
    {
        AuthLog::create([
            "user_type" => "User",
            "user_id" => Auth::guard("user")->id(),
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

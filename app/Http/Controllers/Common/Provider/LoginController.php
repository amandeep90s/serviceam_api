<?php

namespace App\Http\Controllers\Common\Provider;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\AuthLog;
use App\Models\Common\Provider;
use App\Traits\Encryptable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LoginController extends Controller
{
    use Encryptable;

    public function login(Request $request)
    {
        if ($request->has("email")) {
            $request->merge(["email" => strtolower($request->email)]);
        }
        $this->validate($request, [
            "email" => "email|max:255",
            "password" => "required",
            "salt_key" => "required",
        ]);
        if ($request->has("email") && $request->email != "") {
            $request->merge([
                "email" => $this->customEncrypt($request->email, env("DB_SECRET")),
            ]);
        }
        if ($request->has("mobile")) {
            $request->merge([
                "mobile" => $this->customEncrypt(
                    $request->mobile,
                    env("DB_SECRET")
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
                "email" => ["required", "max:255", Rule::exists("providers")],
            ]);
        } elseif (!$request->has("email")) {
            $this->validate(
                $request,
                [
                    "mobile" => ["required", Rule::exists("providers")],
                    "country_code" => "required",
                ],
                [
                    "mobile.exists" => "Please Enter a Valid Mobile Number",
                    "email.exists" => "Please Enter a Valid Email",
                ]
            );
        }
        try {
            $request->request->add([
                "company_id" => base64_decode($request->salt_key),
            ]);
            $request->request->remove("salt_key");
            if ($request->has("email") && $request->email != "") {
                if (
                    !($token = Auth::guard("provider")->attempt(
                        $request->only("email", "password", "company_id")
                    )
                    )
                ) {
                    return Helper::getResponse([
                        "status" => 422,
                        "message" => "Invalid Credentials",
                    ]);
                }
            } else {
                if (
                    !($token = Auth::guard("provider")->attempt(
                        $request->only(
                            "country_code",
                            "mobile",
                            "password",
                            "company_id"
                        )
                    )
                    )
                ) {
                    return Helper::getResponse([
                        "status" => 422,
                        "message" => "Invalid Credentials",
                    ]);
                }
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return Helper::getResponse([
                "status" => 500,
                "message" => "Token Expired",
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return Helper::getResponse([
                "status" => 500,
                "message" => "Token Invalid",
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return Helper::getResponse([
                "status" => 500,
                "message" => $e->getMessage(),
            ]);
        }
        $User = Provider::find(Auth::guard("provider")->user()->id);
        if ($User->activation_status == 0) {
            return Helper::getResponse([
                "status" => 422,
                "message" => "Account Disabled",
            ]);
        }
        $User->device_type = $request->device_type;
        $User->device_token = $request->device_token;
        $User->login_by =
            $request->login_by != null ? $request->login_by : "MANUAL";
        $User->is_online = 1;
        $User->save();
        AuthLog::create([
            "user_type" => "Provider",
            "user_id" => \Auth::guard("provider")->id(),
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
                    "date" => \Carbon\Carbon::now()->format("Y-m-d H:i:s"),
                ],
            ]),
        ]);
        $newUser = Provider::find($User->id);
        $newUser->jwt_token = $token;
        $newUser->save();
        return Helper::getResponse([
            "data" => [
                "token_type" => "Bearer",
                "expires_in" => config("jwt.ttl", "0") * 60,
                "access_token" => $token,
                "user" => $newUser,
            ],
        ]);
    }
}

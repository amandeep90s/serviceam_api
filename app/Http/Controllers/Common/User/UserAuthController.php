<?php

namespace App\Http\Controllers\Common\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserSmsCheckRequest;
use App\Models\Common\AuthLog;
use App\Models\Common\Setting;
use App\Models\Common\User;
use App\Traits\Encryptable;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserAuthController extends Controller
{
    use Encryptable;

    const EMAIL_EXIST_ERROR = "User already registered with given email-Id!";
    const MOBILE_EXIST_ERROR = "User already registered with given mobile number!";
    const DATE_FORMAT = "Y-m-d H:i:s";

    public function refresh(Request $request): JsonResponse
    {
        return Helper::getResponse([
            "data" => [
                "token_type" => "Bearer",
                "expires_in" => config("jwt.ttl", "0") * 60,
                "access_token" => Auth::guard("user")->refresh(),
            ],
        ]);
    }

    public function logout(Request $request)
    {
        try {
            $payload = Auth::guard("user")->payload();
            $token = Auth::guard("user")->tokenById($payload->get("sub"));
            $user = User::find(Auth::guard("user")->user()->id);

            Auth::guard("user")->setToken($token);
            Auth::guard("user")->invalidate();

            $user->jwt_token = null;
            $user->save();

            AuthLog::create([
                "user_type" => "User",
                "user_id" => \Auth::guard("user")->id(),
                "type" => "logout",
                "data" => json_encode([
                    "data" => [
                        $request->getMethod() =>
                            $request->getPathInfo() .
                            " " .
                            $request->getProtocolVersion(),
                        "host" => $request->getHost(),
                        "user_agent" => $request->userAgent(),
                        "date" => Carbon::now()->format(self::DATE_FORMAT),
                    ],
                ]),
            ]);

            return Helper::getResponse([
                "message" => "Successfully logged out",
            ]);
        } catch (JWTException $e) {
            return Helper::getResponse([
                "status" => 403,
                "message" => $e->getMessage(),
            ]);
        }
    }

    public function forgotPasswordOTP(Request $request): JsonResponse
    {
        $account_type = isset($request->account_type)
            ? $request->account_type
            : "";
        if ($account_type == "mobile") {
            $response = $this->forgotPasswordMobile($request);
        } else {
            $response = $this->forgotPasswordEmail($request);
        }
        return $response;
    }

    public function forgotPasswordMobile($request): JsonResponse
    {
        $this->validate($request, [
            "mobile" => "required|numeric|min:6",
            "country_code" => "required",
        ]);
        try {
            $smsData["country_code"] = $request->country_code ?? "";
            $smsData["username"] = $request->mobile ?? "";
            $smsData["account_type"] = $request->account_type ?? "";
            $plusCodeMobileNumber =
                "+" . $smsData["country_code"] . $smsData["username"];
            $request->merge([
                "mobile" => $this->customEncrypt(
                    $request->mobile,
                    config('app.db_secret')
                ),
            ]);
            $request->request->add([
                "company_id" => base64_decode($request->salt_key),
            ]);
            $request->request->remove("salt_key");
            $settings = json_decode(
                json_encode(
                    Setting::where("company_id", $request->company_id)->first()
                        ->settings_data
                )
            );
            $siteConfig = $settings->site;
            $companyId = $request->company_id;
            $otp = mt_rand(100000, 999999);
            $userQuery = User::where("mobile", $request->mobile)->first();
            //User Not Exists
            $validator = Validator::make([], [], []);
            if ($userQuery == null) {
                $validator->errors()->add("mobile", "User not found");
                throw new ValidationException($validator);
            }
            $userQuery->otp = $otp;
            $saveQuery = $userQuery->save();
            if ($saveQuery) {
                $smsData["otp"] = $otp;
                $smsMessage = "Your Otp to reset password is " . $otp;
                if (
                    !empty($siteConfig->send_sms) &&
                    $siteConfig->send_sms == 1
                ) {
                    // send OTP SMS here
                    $result = Helper::sendSms(
                        $companyId,
                        $plusCodeMobileNumber,
                        $smsMessage
                    );
                    $smsData["smsresult"] = $result;
                } else {
                    $errMessage = "SMS configuration disabled";
                }
                return Helper::getResponse([
                    "status" => 200,
                    "message" => "success",
                    "data" => $smsData,
                ]);
            } else {
                $errMessage = trans("admin.something_wrong");
            }
        } catch (Exception $e) {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
        return Helper::getResponse(["status" => 404, "message" => $errMessage]);
    }

    public function forgotPasswordEmail($request): JsonResponse
    {
        $this->validate($request, [
            "email" => "required|email|max:255",
            "salt_key" => "required",
        ]);
        $emailData["username"] = $toEmail = $request->email ?? "";
        $emailData["account_type"] = $request->account_type ?? "";
        try {
            $request->merge([
                "email" => $this->customEncrypt(
                    $request->email,
                    config('app.db_secret')
                ),
            ]);
            $request->request->add([
                "company_id" => base64_decode($request->salt_key),
            ]);
            $request->request->remove("salt_key");
            $settings = json_decode(
                json_encode(
                    Setting::where("company_id", $request->company_id)->first()
                        ->settings_data
                )
            );
            $siteConfig = $settings->site;
            $otp = mt_rand(100000, 999999);
            $userQuery = User::where("email", $request->email)->first();
            //User Not Exists
            $validator = Validator::make([], [], []);
            if ($userQuery == null) {
                $validator->errors()->add("mobile", "User not found");
                throw new ValidationException($validator);
            }
            $userQuery->otp = $otp;
            $userQuery->save();
            $emailData["otp"] = $otp;
            if (
                !empty($siteConfig->send_email) &&
                $siteConfig->send_email == 1
            ) {
                if ($siteConfig->mail_driver == "SMTP") {
                    //  SEND OTP TO MAIL
                    $subject = "Forgot|OTP";
                    $templateFile = "mails/forgotpassmail";
                    $data = [
                        "body" => $otp,
                        "username" => $userQuery->first_name,
                        "salt_key" => $request->company_id,
                    ];
                    Helper::sendEmails(
                        $templateFile,
                        $toEmail,
                        $subject,
                        $data
                    );
                } else {
                    return Helper::getResponse([
                        "status" => 404,
                        "message" => trans("admin.something_wrong"),
                        "error" => "",
                    ]);
                }
            }
            return Helper::getResponse([
                "status" => 200,
                "message" => "success",
                "data" => $emailData,
            ]);
        } catch (Exception $e) {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    public function resetPasswordOTP(Request $request): JsonResponse
    {
        $this->validate($request, [
            "username" => "required",
            "otp" => "required|numeric",
            "account_type" => "required",
            "password" => "required|min:6|confirmed|max:36",
        ]);
        $responseData = $request->all();
        try {
            $account_type = isset($request->account_type)
                ? $request->account_type
                : "";
            $username = isset($request->username) ? $request->username : "";
            $newPassword = isset($request->password) ? $request->password : "";
            $otp = isset($request->otp) ? $request->otp : "";
            $request->merge([
                "loginUser" => $this->customEncrypt(
                    $username,
                    config('app.db_secret')
                ),
            ]);
            if ($account_type == "mobile") {
                $where = ["mobile" => $request->loginUser];
            } else {
                $where = ["email" => $request->loginUser];
            }
            $userQuery = User::where($where)->first();
            //User Not Exists
            $validator = Validator::make([], [], []);
            if ($userQuery == null) {
                $validator->errors()->add("Result", "User not found");
                throw new ValidationException($validator);
            } else {
                $dbOtpCode = $userQuery->otp;
                if ($dbOtpCode != $otp) {
                    $validator->errors()->add("Result", "Invalid Credentials");
                    throw new ValidationException($validator);
                }
                $encNewPassword = Hash::make($newPassword);
                $userQuery->password = $encNewPassword;
                $userQuery->login_by = "MANUAL";
                $userQuery->social_unique_id = null;
                $userQuery->otp = 0;
                $userQuery->save();
            }
            return Helper::getResponse([
                "status" => 200,
                "message" => "Password changed successfully",
                "data" => $responseData,
            ]);
        } catch (Exception $e) {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    public function verify(Request $request): JsonResponse
    {
        if ($request->has("email")) {
            $request->merge([
                "email" => strtolower($request->email),
            ]);
        }

        $this->validate($request, [
            "mobile" => "sometimes",
            "email" => "sometimes|email|max:255",
            "salt_key" => "required",
        ]);

        $company_id = base64_decode($request->salt_key);

        if ($request->has("email") && $request->email != "") {
            $request->merge([
                "email" => $this->customEncrypt(
                    $request->email,
                    config('app.db_secret')
                ),
            ]);

            $email = $request->email;

            $this->validate(
                $request,
                [
                    "email" => [
                        Rule::unique("users")->where(function ($query) use ($email, $company_id) {
                            return $query
                                ->where("email", $email)
                                ->where("company_id", $company_id)
                                ->where("user_type", "NORMAL");
                        }),
                    ],
                ],
                [
                    "email.unique" => self::EMAIL_EXIST_ERROR,
                ]
            );
        }

        if ($request->has("mobile")) {
            $request->merge([
                "mobile" => $this->customEncrypt(
                    $request->mobile,
                    config('app.db_secret')
                ),
            ]);

            $mobile = $request->mobile;
            $country_code = $request->country_code;

            $this->validate(
                $request,
                [
                    "mobile" => [
                        Rule::unique("users")->where(function ($query) use ($mobile, $company_id, $country_code) {
                            return $query
                                ->where("mobile", $mobile)
                                ->where("country_code", $country_code)
                                ->where("company_id", $company_id)
                                ->where("user_type", "NORMAL");
                        }),
                    ],
                ],
                [
                    "mobile.unique" => self::MOBILE_EXIST_ERROR,
                ]
            );
        }

        return Helper::getResponse();
    }

    public function userSmsCheck(UserSmsCheckRequest $request): JsonResponse
    {
        try {
            $otp = mt_rand(100000, 999999);
            $request->request->add([
                "company_id" => base64_decode($request->salt_key),
            ]);
            $settings = $this->getSettings($request);
            $smsMessage =
                "Your" .
                $settings->site->site_title .
                "Otp  password is " .
                $otp;
            $siteConfig = $settings->site;
            $companyId = $request->company_id;
            $send_mail = null;
            $plusCodeMobileNumber = null;

            if ($request->has("mobile")) {
                $plusCodeMobileNumber =
                    "+" . $request->country_code . $request->mobile;
                $request->merge([
                    "mobile" => $this->customEncrypt(
                        $request->mobile,
                        config('app.db_secret')
                    ),
                ]);
                $userQuery = User::where("mobile", $request->mobile)->first();
            } else {
                $send_mail = $request->email;
                $request->merge([
                    "email" => $this->customEncrypt(
                        $request->email,
                        config('app.db_secret')
                    ),
                ]);
                $userQuery = User::where("email", $request->email)->first();
            }

            if ($userQuery == null) {
                $data = $this->handleOtpSending(
                    $request,
                    $siteConfig,
                    $companyId,
                    $plusCodeMobileNumber,
                    $smsMessage,
                    $send_mail,
                    $otp
                );
                return Helper::getResponse([
                    "status" => 200,
                    "message" => "Temporary Password Sent Successfully",
                    "data" => $data,
                ]);
            } else {
                return $this->handleExistingUser($request);
            }
        } catch (Exception $e) {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    private function getSettings($request)
    {
        return json_decode(
            json_encode(
                Setting::where("company_id", $request->company_id)->first()->settings_data
            )
        );
    }

    private function handleOtpSending(
        $request,
        $siteConfig,
        $companyId,
        $plusCodeMobileNumber,
        $smsMessage,
        $send_mail,
        $otp
    ): array
    {
        $data = null;
        if ($request->has("mobile")) {
            if (!empty($siteConfig->send_sms) && $siteConfig->send_sms == 1) {
                $result = Helper::sendSms(
                    $companyId,
                    $plusCodeMobileNumber,
                    $smsMessage
                );
                $data["smsresult"] = $result;
                $data["otp"] = $otp;
            }
        } else {
            if (
                !empty($siteConfig->send_email) &&
                $siteConfig->send_email == 1
            ) {
                $url = $siteConfig->forgot_url . "/user/signup";
                $subject = "Signup|OTP";
                $templateFile = "mails/signupotp";
                $data = [
                    "body" => $otp,
                    "subject" => $subject,
                    "templateFile" => $templateFile,
                    "send_mail" => $send_mail,
                    "salt_key" => $companyId,
                    "site_url" => $url,
                ];
                Helper::signupOtp($data);
            }
        }
        return $data;
    }

    private function handleExistingUser($request)
    {
        if ($request->has("mobile")) {
            return Helper::getResponse([
                "status" => 201,
                "message" => "Mobile Number Already Exist",
            ]);
        } else {
            return Helper::getResponse([
                "status" => 201,
                "message" => "Email Already Exist",
            ]);
        }
    }
}

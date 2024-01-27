<?php

namespace App\Helpers;

use App\Models\Common\RequestLog;
use App\Models\Common\Setting;
use Endroid\QrCode\Bacon\ErrorCorrectionLevelConverter;
use Endroid\QrCode\QrCode;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class Helper
{
    public static function getUsername(Request $request): string
    {
        $username = "";

        if (isset($request->mobile)) {
            $username = "mobile";
        } elseif (isset($request->email)) {
            $username = "email";
        }

        return $username;
    }

    public static function currencyFormat($value = "", $symbol = ""): string
    {
        return $value == ""
            ? $symbol . number_format(0, 2, ".", "")
            : $symbol . number_format($value, 2, ".", "");
    }

    public static function decimalRoundOff($value): string
    {
        return number_format($value, 2, ".", "");
    }

    public static function upload_file(
        $picture,
        $path,
        $file = null,
        $company_id = null
    ): string {
        if ($file == null) {
            $file_name = time();
            $file_name .= rand();
            $file_name = sha1($file_name);

            $file = $file_name . "." . $picture->getClientOriginalExtension();
        }

        if (!empty(Auth::user())) {
            $company_id = Auth::user()->company_id;
        }

        $path = $company_id . "/" . $path;

        if (!file_exists(app()->basePath("storage/app/public/" . $path))) {
            mkdir(app()->basePath("storage/app/public/" . $path), 0777, true);
        }

        return url() . "/storage/" . $picture->storeAs($path, $file);
    }

    public static function upload_providerfile(
        $picture,
        $path,
        $file = null,
        $company_id = null
    ): string {
        if ($file == null) {
            $file_name = time();
            $file_name .= rand();
            $file_name = sha1($file_name);

            $file = $file_name . "." . $picture->getClientOriginalExtension();
        }

        $path =
            ($company_id == null
                ? Auth::guard("provider")->user()->company_id
                : $company_id) .
            "/" .
            $path;

        if (!file_exists(app()->basePath("storage/app/public/" . $path))) {
            mkdir(app()->basePath("storage/app/public/" . $path), 0777, true);
        }

        return url() . "/storage/" . $picture->storeAs($path, $file);
    }

    public static function curl($url): bool|string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }

    public static function generate_booking_id($prefix): string
    {
        return $prefix . mt_rand(100000, 999999);
    }

    public static function getDistanceMap($source, $destination)
    {
        $settings = Helper::setting();
        $siteConfig = $settings->site;

        $map = file_get_contents(
            "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" .
                implode("|", $source) .
                "&destinations=" .
                implode("|", $destination) .
                "&sensor=false&key=" .
                $siteConfig->server_key
        );
        return json_decode($map);
    }

    // public static function setting($company_id = null)
    // {
    //     $id =
    //         $company_id == null
    //         ? Auth::guard(strtolower(self::getGuard()))->user()->company_id
    //         : $company_id;

    //     $setting = Setting::where("company_id", $id)->first();
    //     $settings = json_decode(json_encode($setting->settings_data));
    //     $settings->demo_mode = $setting->demo_mode;
    //     return $settings;
    // }

    public static function setting($company_id = null)
    {
        $user = Auth::guard(strtolower(self::getGuard()))->user();

        if ($user) {
            $id = $company_id ?? $user->company_id;

            $setting = Setting::where("company_id", $id)->first();

            if ($setting) {
                $settings = json_decode(json_encode($setting->settings_data));
                $settings->demo_mode = $setting->demo_mode;
                return $settings;
            } else {
                // Handle case where no setting is found for the specified company_id
                // You might want to return a default setting or throw an exception
            }
        } else {
            // Handle case where the user is not authenticated
            // You might want to return a default setting or throw an exception
        }
    }


    public static function getGuard()
    {
        if (Auth::guard("admin")->check()) {
            return strtoupper("admin");
        } elseif (Auth::guard("provider")->check()) {
            return strtoupper("provider");
        } elseif (Auth::guard("user")->check()) {
            return strtoupper("user");
        }
    }

    public static function encryptResponse($response = []): JsonResponse
    {
        $status = !empty($response["status"]) ? $response["status"] : 200;
        $title = !empty($response["title"])
            ? $response["title"]
            : self::getStatus($status);
        $message = !empty($response["message"]) ? $response["message"] : "";
        $responseData = !empty($response["data"])
            ? self::my_encrypt(
                "FbcCY2yCFBwVCUE9R+6kJ4fAL4BJxxjd",
                json_encode($response["data"])
            )
            : [];
        $error = !empty($response["error"]) ? $response["error"] : [];

        if ($status != 401 && $status != 405 && $status != 422) {
            RequestLog::create([
                "data" => json_encode([
                    "request" => app("request")->request->all(),
                    "response" => $message,
                    "error" => $error,
                    "responseCode" => $status,
                    $_SERVER["REQUEST_METHOD"] =>
                    $_SERVER["REQUEST_URI"] .
                        " " .
                        $_SERVER["SERVER_PROTOCOL"],
                    "host" => $_SERVER["HTTP_HOST"],
                    "ip" => $_SERVER["REMOTE_ADDR"],
                    "user_agent" => $_SERVER["HTTP_USER_AGENT"],
                    "date" => \Carbon\Carbon::now()->format("Y-m-d H:i:s"),
                ]),
            ]);
        }

        return response()->json(
            [
                "statusCode" => (string)$status,
                "title" => $title,
                "message" => $message,
                "responseData" => $responseData,
                "error" => $error,
            ],
            $status
        );
    }

    public static function getStatus($code)
    {
        switch ($code) {
            case 200:
                return "OK";
            case 201:
                return "Created";
            case 204:
                return "No Content";
            case 301:
                return "Moved Permanently";
            case 400:
                return "Bad Request";
            case 401:
                return "Unauthorized";
            case 403:
                return "Forbidden";
            case 404:
                return "Not Found";
            case 405:
                return "Method Not Allowed";
            case 422:
                return "Unprocessable Entity";
            case 500:
                return "Internal Server Error";
            case 502:
                return "Bad Gateway";
            case 503:
                return "Service Unavailable";
            default:
                return "Unknown Error";
        }
    }

    public static function my_encrypt($passphrase, $encrypt): array
    {
        $salt = openssl_random_pseudo_bytes(128);
        $iv = openssl_random_pseudo_bytes(16);
        //on PHP7 can use random_bytes() istead openssl_random_pseudo_bytes()
        //or PHP5x see : https://github.com/paragonie/random_compat

        $iterations = 999;
        $key = hash_pbkdf2("sha1", $passphrase, $salt, $iterations, 64);

        $encrypted_data = openssl_encrypt(
            $encrypt,
            "aes-128-cbc",
            hex2bin($key),
            OPENSSL_RAW_DATA,
            $iv
        );

        return [
            "ciphertext" => base64_encode($encrypted_data),
            "iv" => bin2hex($iv),
            "salt" => bin2hex($salt),
        ];
    }

    public static function getResponse($response = []): JsonResponse
    {
        $status = !empty($response["status"]) ? $response["status"] : 200;
        $title = !empty($response["title"])
            ? $response["title"]
            : self::getStatus($status);
        $message = !empty($response["message"]) ? $response["message"] : "";
        $responseData = !empty($response["data"]) ? $response["data"] : [];
        $error = !empty($response["error"]) ? $response["error"] : [];

        if ($status != 401 && $status != 405 && $status != 422) {
            app("request")->request->remove("picture");
            app("request")->request->remove("file");
            app("request")->request->remove("vehicle_image");
            app("request")->request->remove("vehicle_marker");

            RequestLog::create([
                "data" => json_encode([
                    "request" => app("request")->request->all(),
                    "response" => $message,
                    "error" => $error,
                    "responseCode" => $status,
                    $_SERVER["REQUEST_METHOD"] =>
                    $_SERVER["REQUEST_URI"] .
                        " " .
                        $_SERVER["SERVER_PROTOCOL"],
                    "host" => $_SERVER["HTTP_HOST"],
                    "ip" => $_SERVER["REMOTE_ADDR"],
                    "user_agent" => $_SERVER["HTTP_USER_AGENT"],
                    "date" => \Carbon\Carbon::now()->format("Y-m-d H:i:s"),
                ]),
            ]);
        }

        return response()->json(
            [
                "statusCode" => (string)$status,
                "title" => $title,
                "message" => $message,
                "responseData" => $responseData,
                "error" => $error,
            ],
            $status
        );
    }

    public static function delete_picture($picture): bool
    {
        $url = app()->basePath("storage/") . $picture;
        @unlink($url);
        return true;
    }

    /**
     * @throws ConfigurationException
     */
    public static function send_sms(
        $companyId,
        $plusCodeMobileNumber,
        $smsMessage
    ): Exception|int|TwilioException {
        //  SEND OTP TO REGISTER MEMBER
        $settings = json_decode(
            json_encode(
                Setting::where("company_id", $companyId)->first()->settings_data
            )
        );
        $siteConfig = $settings->site;
        $accountSid = $siteConfig->sms_account_sid;
        $authToken = $siteConfig->sms_auth_token;
        $twilioNumber = $siteConfig->sms_from_number;

        $client = new Client($accountSid, $authToken);

        $tousernumber = $plusCodeMobileNumber;

        try {
            $client->messages->create($tousernumber, [
                "body" => $smsMessage,
                "from" => $twilioNumber,
                //   On US phone numbers, you could send an image as well!
                //  'mediaUrl' => $imageUrl
            ]);
            Log::info(
                "Message sent to " .
                    $plusCodeMobileNumber .
                    "from " .
                    $twilioNumber
            );
            return 1;
        } catch (TwilioException $e) {
            Log::error(
                "Could not send SMS notification." .
                    " Twilio replied with: " .
                    $e
            );
            return $e;
        }
    }

    public static function siteRegisterMail($user): bool
    {
        $settings = json_decode(
            json_encode(
                Setting::where("company_id", $user->company_id)->first()
                    ->settings_data
            )
        );

        Mail::send(
            "mails.welcome",
            ["user" => $user, "settings" => $settings],
            function ($mail) use ($user, $settings) {
                $mail->from(
                    $settings->site->mail_from_address,
                    $settings->site->mail_from_name
                );
                $mail
                    ->to(
                        $user->email,
                        $user->first_name . " " . $user->last_name
                    )
                    ->subject("Welcome");
            }
        );

        return true;
    }

    public static function signup_otp($user): bool
    {
        $settings = json_decode(
            json_encode(
                Setting::where("company_id", $user["salt_key"])->first()
                    ->settings_data
            )
        );

        Mail::send(
            $user["templateFile"],
            ["user" => $user, "settings" => $settings],
            function ($mail) use ($user, $settings) {
                $mail->from(
                    $settings->site->mail_from_address,
                    $settings->site->mail_from_name
                );
                $mail->to($user["send_mail"])->subject("OTP for Registeration");
            }
        );

        return true;
    }

    /**
     * @throws Exception
     */
    public static function send_emails(
        $templateFile,
        $toEmail,
        $subject,
        $data
    ): bool {
        try {
            if (isset($data["salt_key"])) {
                $settings = json_decode(
                    json_encode(
                        Setting::where("company_id", $data["salt_key"])->first()
                            ->settings_data
                    )
                );
            } else {
                if (!empty(Auth::user())) {
                    $company_id = Auth::user()->company_id;
                }
                $settings = json_decode(
                    json_encode(
                        Setting::where("company_id", $company_id)->first()
                            ->settings_data
                    )
                );
            }
            $data["settings"] = $settings;
            $mail = Mail::send("$templateFile", $data, function ($message) use (
                $data,
                $toEmail,
                $subject,
                $settings
            ) {
                $message->from(
                    $settings->site->mail_from_address,
                    $settings->site->mail_from_name
                );
                $message->to($toEmail)->subject($subject);
            });

            if (count(Mail::failures()) > 0) {
                throw new Exception("Error: Mail sent failed!");
            } else {
                return true;
            }
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Send email job method
     * @throws Exception
     */
    public static function sendEmailsJob($templateFile, $toEmail, $subject, $data): bool
    {
        try {
            Mail::send($templateFile, $data, function ($message) use ($toEmail, $subject) {
                $message->from("dev@appoets.com", "GOX");
                $message->to($toEmail)->subject($subject);
            });

            if (count(Mail::failures()) > 0) {
                throw new \Exception("Error: Mail sent failed!");
            }

            return true;
        } catch (\Throwable $e) {
            // Log the exception or handle it appropriately
            throw new \Exception($e->getMessage());
        }
    }
}

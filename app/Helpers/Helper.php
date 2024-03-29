<?php

namespace App\Helpers;

use App\Models\Common\RequestLog;
use App\Models\Common\Setting;
use Carbon\Carbon;
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
        // Check if the request has an email, if not, set it as an empty string
        $email = $request->email ? "email" : "";

        // If the request has a mobile number, return "mobile", otherwise return the email
        return $request->mobile ? "mobile" : $email;
    }

    public static function currencyFormat($value = "", $symbol = ""): string
    {
        // Format the given value as a currency, prepend the symbol and return it
        return $symbol . number_format($value ?: 0, 2, ".", "");
    }

    public static function decimalRoundOff($value): string
    {
        // Round off the given value to two decimal places and return it
        return number_format($value, 2, ".", "");
    }

    public static function uploadFile($picture, $path, $file = null, $company_id = null): string
    {
        // Generate a file name if not provided
        $file = $file ?: sha1(time() . rand()) . "." . $picture->getClientOriginalExtension();

        // Use the authenticated user's company id if not provided
        $company_id = $company_id ?: Auth::user()->company_id;

        // Define the storage path
        $path = $company_id . '/' . $path;

        // Store the picture at the specified path with the given file name
        $picture->storeAs($path, $file);

        // Return the asset URL of the stored picture
        return asset('storage/' . $path . '/' . $file);
    }

    public static function uploadProviderFile($picture, $path, $file = null, $company_id = null): string
    {
        // Generate a file name if not provided
        $file = $file ?: sha1(time() . rand()) . "." . $picture->getClientOriginalExtension();

        // Use the authenticated provider's company id if not provided
        $company_id = $company_id ?: Auth::guard("provider")->user()->company_id;

        // Define the storage path
        $path = $company_id . "/" . $path;

        // If the storage path does not exist, create it
        if (!file_exists(app()->basePath("storage/app/public/" . $path))) {
            mkdir(app()->basePath("storage/app/public/" . $path), 0777, true);
        }

        // Store the picture at the specified path with the given file name and return its URL
        return url() . "/storage/" . $picture->storeAs($path, $file);
    }

    public static function curl($url): bool|string
    {
        // Initialize a new cURL session
        $ch = curl_init();

        // Set the URL to fetch
        curl_setopt($ch, CURLOPT_URL, $url);

        // Set the option to return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // Execute the cURL session
        $return = curl_exec($ch);

        // Close the cURL session
        curl_close($ch);

        // Return the result
        return $return;
    }

    public static function generateBookingId(string $prefix): string
    {
        // Generate a random number between 100000 and 999999, prepend the prefix and return it
        return $prefix . mt_rand(100000, 999999);
    }

    public static function getDistanceMap(array $source, array $destination): object
    {
        // Get the site settings
        $siteConfig = self::setting()->site;

        // Fetch the distance matrix from Google Maps API and return it as an object
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

    public static function setting($company_id = null)
    {
        // Get the authenticated user
        $user = Auth::guard(strtolower(self::getGuard()))->user();

        // If a user is authenticated
        if ($user) {
            // Use the provided company id or default to the authenticated user's company id
            $id = $company_id ?? $user->company_id;

            // Fetch the settings for the specified company id
            $setting = Setting::where("company_id", $id)->first();

            // If settings are found
            if ($setting) {
                // Decode the settings data and add the demo mode to it
                $settings = json_decode(json_encode($setting->settings_data));
                $settings->demo_mode = $setting->demo_mode;

                // Return the settings
                return $settings;
            }
        }
    }

    public static function getGuard(): string
    {
        // Initialize the guard variable
        $guard = '';

        // Check which guard is authenticated and set the guard variable accordingly
        if (Auth::guard("admin")->check()) {
            $guard = strtoupper("admin");
        } elseif (Auth::guard("provider")->check()) {
            $guard = strtoupper("provider");
        } elseif (Auth::guard("user")->check()) {
            $guard = strtoupper("user");
        }

        // Return the guard
        return $guard;
    }

    public static function encryptResponse(array $response = []): JsonResponse
    {
        $status = $response["status"] ?? 200;
        $title = $response["title"] ?? self::getStatus($status);
        $message = $response["message"] ?? "";
        $responseData = $response["data"]
            ? self::myEncrypt("FbcCY2yCFBwVCUE9R+6kJ4fAL4BJxxjd", json_encode($response["data"]))
            : [];
        $error = $response["error"] ?? [];

        if (!in_array($status, [401, 405, 422])) {
            RequestLog::create([
                "data" => json_encode([
                    "request" => app("request")->request->all(),
                    "response" => $message,
                    "error" => $error,
                    "responseCode" => $status,
                    $_SERVER["REQUEST_METHOD"] => $_SERVER["REQUEST_URI"] . " " . $_SERVER["SERVER_PROTOCOL"],
                    "host" => $_SERVER["HTTP_HOST"],
                    "ip" => $_SERVER["REMOTE_ADDR"],
                    "user_agent" => $_SERVER["HTTP_USER_AGENT"],
                    "date" => Carbon::now()->format("Y-m-d H:i:s"),
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

    public static function getStatus(int $code): string
    {
        $statusCodes = [
            200 => "OK",
            201 => "Created",
            204 => "No Content",
            301 => "Moved Permanently",
            400 => "Bad Request",
            401 => "Unauthorized",
            403 => "Forbidden",
            404 => "Not Found",
            405 => "Method Not Allowed",
            422 => "Unprocessable Entity",
            500 => "Internal Server Error",
            502 => "Bad Gateway",
            503 => "Service Unavailable",
        ];

        return $statusCodes[$code] ?? "Unknown Error";
    }

    public static function myEncrypt(string $passphrase, string $encrypt): array
    {
        $salt = random_bytes(128);
        $iv = random_bytes(16);
        $iterations = 999;
        $key = hash_pbkdf2("sha1", $passphrase, $salt, $iterations, 64);

        $encryptedData = openssl_encrypt(
            $encrypt,
            "aes-128-cbc",
            hex2bin($key),
            OPENSSL_RAW_DATA,
            $iv
        );

        return [
            "ciphertext" => base64_encode($encryptedData),
            "iv" => bin2hex($iv),
            "salt" => bin2hex($salt),
        ];
    }

    public static function deletePicture(string $picture): bool
    {
        $url = app()->basePath("storage/") . $picture;
        @unlink($url);
        return true;
    }

    /**
     * @throws ConfigurationException
     */
    public static function sendSms(
        int    $companyId,
        string $plusCodeMobileNumber,
        string $smsMessage
    ): Exception|int|TwilioException
    {
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

        try {
            $client->messages->create($plusCodeMobileNumber, [
                "body" => $smsMessage,
                "from" => $twilioNumber,
            ]);
            Log::info("Message sent to $plusCodeMobileNumber from $twilioNumber");
            return 1;
        } catch (TwilioException $e) {
            Log::error("Could not send SMS notification. Twilio replied with: $e");
            return $e;
        }
    }

    public static function siteRegisterMail($user): bool
    {
        $settings = self::getSettings($user->company_id);

        Mail::send(
            "mails.welcome",
            ["user" => $user, "settings" => $settings],
            function ($mail) use ($user, $settings) {
                $mail->from($settings->site->mail_from_address, $settings->site->mail_from_name)
                    ->to($user->email, $user->first_name . " " . $user->last_name)
                    ->subject("Welcome");
            }
        );

        return true;
    }

    private static function getSettings(int $companyId): object
    {
        return json_decode(
            json_encode(
                Setting::where("company_id", $companyId)->first()->settings_data
            )
        );
    }

    public static function signupOtp(array $user): bool
    {
        $settings = self::getSettings($user["salt_key"]);

        Mail::send(
            $user["templateFile"],
            ["user" => $user, "settings" => $settings],
            function ($mail) use ($user, $settings) {
                $mail->from($settings->site->mail_from_address, $settings->site->mail_from_name)
                    ->to($user["send_mail"])
                    ->subject("OTP for Registration");
            }
        );

        return true;
    }

    public static function logger($input): void
    {
        print_r($input);
        die();
    }

    /**
     * @throws Exception
     */
    public static function sendEmails(
        string $templateFile,
        string $toEmail,
        string $subject,
        array  $data
    ): bool|JsonResponse
    {
        try {
            $companyId = $data["salt_key"] ?? (Auth::user() ? Auth::user()->company_id : null);
            if (!$companyId) {
                return self::getResponse([
                    "status" => 404,
                    "message" => "Company ID not found",
                ]);
            }

            $settings = self::getSettings($companyId);
            $data["settings"] = $settings;

            Mail::send($templateFile, $data, function ($message) use ($toEmail, $subject, $settings) {
                $message->from($settings->site->mail_from_address, $settings->site->mail_from_name)
                    ->to($toEmail)
                    ->subject($subject);
            });

            return Mail::failures() ? self::getResponse([
                "status" => 500,
                "message" => "Error: Mail sent failed!",
            ]) : true;
        } catch (\Throwable $e) {
            return self::getResponse([
                "status" => 500,
                "message" => $e->getMessage()
            ]);
        }
    }

    public static function getResponse(array $response = []): JsonResponse
    {
        $status = $response["status"] ?? 200;
        $title = $response["title"] ?? self::getStatus($status);
        $message = $response["message"] ?? "";
        $responseData = $response["data"] ?? [];
        $error = $response["error"] ?? [];

        if (!in_array($status, [401, 405, 422])) {
            $request = app("request")->request;
            $request->remove("picture");
            $request->remove("file");
            $request->remove("vehicle_image");
            $request->remove("vehicle_marker");

            RequestLog::create([
                "data" => json_encode([
                    "request" => $request->all(),
                    "response" => $message,
                    "error" => $error,
                    "responseCode" => $status,
                    $_SERVER["REQUEST_METHOD"] => $_SERVER["REQUEST_URI"] . " " . $_SERVER["SERVER_PROTOCOL"],
                    "host" => $_SERVER["HTTP_HOST"],
                    "ip" => $_SERVER["REMOTE_ADDR"],
                    "user_agent" => $_SERVER["HTTP_USER_AGENT"],
                    "date" => Carbon::now()->format("Y-m-d H:i:s"),
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

    public static function sendEmailsJob(
        string $templateFile,
        string $toEmail,
        string $subject,
        array  $data
    ): bool|JsonResponse
    {
        try {
            Mail::send($templateFile, $data, function ($message) use ($toEmail, $subject) {
                $message->from("dev@appoets.com", "GOX")
                    ->to($toEmail)
                    ->subject($subject);
            });

            if (Mail::failures()) {
                return self::getResponse([
                    "status" => 500,
                    "message" => "Error: Mail sent failed!",
                ]);
            }

            return true;
        } catch (\Throwable $e) {
            return self::getResponse([
                "status" => 500,
                "message" => $e->getMessage()
            ]);
        }
    }

    public static function qrCode(
        string $data,
        string $file,
        int    $companyId,
        string $path = 'qr_code/',
        int    $size = 500,
        int    $margin = 10
    ): string
    {
        return true;
        /* TODO
        $qrCode = QrCode::create($data)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->setSize($size)
            ->setMargin($margin)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $filePath = app()->basePath("storage/app/public/$companyId/$path");

        if (!file_exists($filePath)) {
            mkdir($filePath, 0777, true);
        }


        $writer = new PngWriter();

        $filePathWithFileName = $filePath . $file;

        $logo = Logo::create(__DIR__ . '/assets/symfony.png')
            ->setResizeToWidth(50)
            ->setPunchoutBackground(true);

        $label = Label::create('Label')
            ->setTextColor(new Color(255, 0, 0));

        $result = $writer->write($qrCode, $logo, $label);

        // Save it to a file
        $result->saveToFile($filePathWithFileName);

        return url("/storage/$companyId/$path$file");
        */
    }

    /**
     * Encode and Decode a string into an array
     */
    public static function jsonEncodeDecode($string)
    {
        return json_decode(json_encode($string));
    }
}

<?php

namespace App\Http\Controllers\Common;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\AdminService;
use App\Models\Common\AuthLog;
use App\Models\Common\AuthMobileOtp;
use App\Models\Common\Chat;
use App\Models\Common\City;
use App\Models\Common\CmsPage;
use App\Models\Common\Company;
use App\Models\Common\Country;
use App\Models\Common\FleetWallet;
use App\Models\Common\ProviderWallet;
use App\Models\Common\Rating;
use App\Models\Common\Setting;
use App\Models\Common\State;
use App\Models\Common\UserRequest;
use App\Models\Common\UserWallet;
use App\Models\Service\ServiceCategory;
use App\Services\SendPushNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Twilio\Exceptions\ConfigurationException;

class CommonController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $data = ServiceCategory::where(
            "service_category_name",
            "like",
            "%" . $request->search . "%"
        )
            ->where("service_category_status", 1)
            ->where("favorites", 1)
            ->orderBy("service_category_order", "asc")
            ->get();
        return Helper::getResponse(["data" => $data]);
    }

    /**
     * @throws ValidationException
     */
    public function base(Request $request)
    {
        $this->validate($request, [
            "salt_key" => "required",
        ]);

        $license = Company::find(base64_decode($request->salt_key));

        if ($license != null) {
            try {
                if (Carbon::parse($license->expiry_date)->lt(Carbon::now())) {
                    return response()->json(
                        ["message" => "License Expired"],
                        503
                    );
                }

                $admin_service = AdminService::where("company_id", $license->id)
                    ->where("status", 1)
                    ->get();

                $base_url = $license->base_url;

                $setting = Setting::where("company_id", $license->id)->first();
                $settings = json_decode(json_encode($setting->settings_data));

                $appSettings = [];
                if (count($settings) > 0) {
                    $appSettings["demo_mode"] = (int)$setting->demo_mode;
                    $appSettings["provider_negative_balance"] = $settings->site->provider_negative_balance ?? "";
                    $appSettings["android_key"] = $settings->site->android_key ?? "";
                    $appSettings["ios_key"] = $settings->site->ios_key ?? "";
                    $appSettings["referral"] =
                        $settings->site->referral == 1 ? 1 : 0;

                    $appSettings["social_login"] =
                        $settings->site->social_login == 1 ? 1 : 0;
                    $appSettings["otp_verify"] =
                        $settings->transport->ride_otp == 1 ? 1 : 0;

                    $appSettings["ride_otp"] =
                        $settings->transport->ride_otp == 1 ? 1 : 0;

                    $appSettings["order_otp"] =
                        $settings->order->order_otp == 1 ? 1 : 0;

                    $appSettings["service_otp"] =
                        $settings->service->serve_otp == 1 ? 1 : 0;
                    $appSettings["payments"] =
                        count($settings->payment) > 0 ? $settings->payment : 0;

                    $appSettings["cmspage"]["privacypolicy"] = $settings->site->page_privacy ?? 0;
                    $appSettings["cmspage"]["help"] = $settings->site->help ?? 0;
                    $appSettings["cmspage"]["terms"] = $settings->site->terms ?? 0;
                    $appSettings["cmspage"]["cancel"] = $settings->site->cancel ?? 0;
                    $appSettings["supportdetails"]["contact_number"] =
                        isset($settings->site->contact_number) > 0
                            ? $settings->site->contact_number
                            : 0;
                    $appSettings["supportdetails"]["contact_email"] =
                        isset($settings->site->contact_email) > 0
                            ? $settings->site->contact_email
                            : 0;
                    $appSettings["languages"] =
                        isset($settings->site->language) > 0
                            ? $settings->site->language
                            : 0;
                }
                return Helper::getResponse([
                    "status" => 200,
                    "data" => [
                        "base_url" => $base_url,
                        "services" => $admin_service,
                        "appsetting" => $appSettings,
                    ],
                ]);
            } catch (\Exception $e) {
                return Helper::getResponse([
                    "status" => 500,
                    "message" => trans("Something Went Wrong"),
                    "error" => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function logdata($type, $id): JsonResponse
    {
        $date = Carbon::today()->subDays(7);

        $datum = AuthLog::where("user_type", $type)
            ->where("user_id", $id)
            ->orderBy("created_at", "DESC")
            ->whereDate("created_at", ">", $date)
            ->paginate(5);

        return Helper::getResponse(["data" => $datum]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function walletDetails($type, $id): JsonResponse
    {
        $date = Carbon::today()->subDays(15);

        $data = null;

        if ($type == "User") {
            $data = UserWallet::with("user")
                ->where("user_id", $id)
                ->select(
                    "*",
                    \DB::raw("DATEDIFF(now(),created_at) as days"),
                    \DB::raw("TIMEDIFF(now(),created_at) as total_time")
                );
        } elseif ($type == "Provider") {
            $data = ProviderWallet::with("provider")->where(
                "provider_id",
                $id
            );
        } elseif ($type == "Fleet") {
            $data = FleetWallet::where("fleet_id", $id);
        }

        $wallet_details = $data !== null
            ? $data->orderBy("created_at", "DESC")
                ->whereDate("created_at", ">", $date)
                ->paginate(10)
            : [];

        return Helper::getResponse(["data" => $wallet_details]);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function rating(Request $request): bool
    {
        Rating::create([
            "company_id" => $request->company_id,
            "admin_service" => $request->admin_service,
            "provider_id" => $request->provider_id,
            "user_id" => $request->user_id,
            "request_id" => $request->id,
            "user_rating" => $request->rating,
            "user_comment" => $request->comment,
        ]);

        return true;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function admin_services(Request $request): JsonResponse
    {
        $admin_service = AdminService::where(
            "company_id",
            Auth::user()->company_id
        )
            ->whereNotIn("admin_service", ["ORDER"])
            ->where("status", 1)
            ->get();

        return Helper::getResponse(["status" => 200, "data" => $admin_service]);
    }

    /**
     * Display a listing of the resource.
     * @throws ValidationException|ConfigurationException
     */
    public function sendOtp(Request $request)
    {
        $this->validate($request, [
            "salt_key" => "required",
        ]);

        $company_id = base64_decode($request->salt_key);

        $otp = $this->createOtp($company_id);

        $settings = json_decode(
            json_encode(
                Setting::where("company_id", $company_id)->first()
                    ->settings_data
            )
        );

        $siteConfig = $settings->site;

        if ($request->has("mobile")) {
            AuthMobileOtp::updateOrCreate(
                [
                    "company_id" => $company_id,
                    "country_code" => $request->country_code,
                    "mobile" => $request->mobile,
                ],
                ["otp" => $otp]
            );

            $send_sms = Helper::send_sms(
                $company_id,
                "+" . $request->country_code . "" . $request->mobile,
                "Your OTP is " . $otp . ". Do not share your OTP with anyone"
            );

            if ($send_sms == 1) {
                return Helper::getResponse(["message" => "OTP sent! " . $otp]);
            } else {
                return Helper::getResponse([
                    "status" => "400",
                    "message" =>
                        "Could not send SMS notification. Please try again!",
                    "error" => $send_sms,
                ]);
            }
        } else {
            if (
                !empty($siteConfig->send_email) &&
                $siteConfig->send_email == 1
            ) {
                AuthMobileOtp::updateOrCreate(
                    ["company_id" => $company_id, "email" => $request->email],
                    ["otp" => $otp]
                );

                $subject = "Signup|OTP";
                $templateFile = "mails/signupotp";
                $data = [
                    "body" => $otp,
                    "subject" => $subject,
                    "templateFile" => $templateFile,
                    "send_mail" => $request->email,
                    "salt_key" => $company_id,
                ];
                $result = Helper::signup_otp($data);


                return $result == 1
                    ? Helper::getResponse([
                        "message" => "OTP sent! " . $otp,
                    ])
                    : Helper::getResponse([
                        "status" => "400",
                        "message" =>
                            "Could not send SMS notification. Please try again!",
                        "error" => $result,
                    ]);

            }
        }
    }

    /**
     * @param $company_id
     * @return int|void
     */
    public function createOtp($company_id)
    {
        $otp = mt_rand(1111, 9999);

        $auth_mobile_otp = AuthMobileOtp::select("id")
            ->where("otp", $otp)
            ->where("company_id", $company_id)
            ->orderBy("id", "desc")
            ->first();

        if ($auth_mobile_otp != null) {
            $this->createOtp($company_id);
        } else {
            return $otp;
        }
    }

    /**
     * Display a listing of the resource.
     * @throws ValidationException
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $this->validate($request, [
            "otp" => "required",
            "salt_key" => "required",
        ]);

        $company_id = base64_decode($request->salt_key);

        if ($request->has("mobile")) {
            $this->validate($request, [
                "country_code" => "required",
                "mobile" => "required",
            ]);

            $auth_mobile_otp = AuthMobileOtp::where(
                "country_code",
                $request->country_code
            )
                ->where("mobile", $request->mobile)
                ->where("otp", $request->otp)
                ->where("updated_at", ">=", Carbon::now()->subMinutes(10))
                ->where("company_id", $company_id)
                ->first();
        } else {
            $this->validate($request, [
                "email" => "required",
            ]);

            $auth_mobile_otp = AuthMobileOtp::where("email", $request->email)
                ->where("otp", $request->otp)
                ->where("updated_at", ">=", Carbon::now()->subMinutes(10))
                ->where("company_id", $company_id)
                ->first();
        }

        if ($auth_mobile_otp != null) {
            $auth_mobile_otp->delete();

            return Helper::getResponse(["message" => "OTP sent!"]);
        } else {
            return Helper::getResponse([
                "status" => "400",
                "message" => "OTP error!",
            ]);
        }
    }

    /**
     * @return JsonResponse
     */
    public function countries_list(): JsonResponse
    {
        $countries = Country::get();
        return Helper::getResponse(["data" => $countries]);
    }

    /**
     * Display a listing of the resource.
     */
    public function states_list($id): JsonResponse
    {
        $states = State::where("country_id", $id)->get();
        return Helper::getResponse(["data" => $states]);
    }

    /**
     * Display a listing of the resource.
     */
    public function cities_list($id): JsonResponse
    {
        $cities = City::where("state_id", $id)->get();
        return Helper::getResponse(["data" => $cities]);
    }

    /**
     * Display a listing of the resource.
     * @throws ValidationException
     */
    public function chat(Request $request): JsonResponse
    {
        $this->validate($request, [
            "id" => "required",
            "admin_service" => "required|in:TRANSPORT,ORDER,SERVICE",
            "salt_key" => "required",
            "user_name" => "required",
            "provider_name" => "required",
            "type" => "required",
            "message" => "required",
        ]);

        $company_id = base64_decode($request->salt_key);

        $user_request = UserRequest::where("request_id", $request->id)
            ->where("admin_service", $request->admin_service)
            ->where("company_id", $company_id)
            ->first();

        if ($user_request != null) {
            $chat = Chat::where("admin_service", $request->admin_service)
                ->where("request_id", $request->id)
                ->where("company_id", $company_id)
                ->first();

            if ($chat != null) {
                $data = $chat->data;
                $data[] = [
                    "type" => $request->type,
                    "user" => $request->user_name,
                    "provider" => $request->provider_name,
                    "message" => $request->message,
                ];
                $chat->data = json_encode($data);
                $chat->save();
            } else {
                $chat = new Chat();
                $data[] = [
                    "type" => $request->type,
                    "user" => $request->user_name,
                    "provider" => $request->provider_name,
                    "message" => $request->message,
                ];
                $chat->admin_service = $request->admin_service;
                $chat->request_id = $request->id;
                $chat->company_id = $company_id;
                $chat->data = json_encode($data);
                $chat->save();
            }

            if ($request->type == "user") {
                (new SendPushNotification())->ChatPushProvider(
                    $user_request->provider_id,
                    "chat_" . strtolower($chat->admin_service)
                );
            } elseif ($request->type == "provider") {
                (new SendPushNotification())->ChatPushUser(
                    $user_request->user_id,
                    "chat_" . strtolower($chat->admin_service)
                );
            }

            return Helper::getResponse(["message" => "Successfully Inserted!"]);
        } else {
            return Helper::getResponse([
                "status" => 400,
                "message" => "No service found!",
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function cmspagetype($type): JsonResponse
    {
        $cities = CmsPage::where("page_name", $type)
            ->where()
            ->get();
        return Helper::getResponse(["data" => $cities]);
    }
}

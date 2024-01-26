<?php

namespace App\Http\Controllers\Service\User;

use App\Helpers\Helper;
use App\Http\Controllers\Common\CommonController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\Provider\ServiceController as ProviderServiceController;
use App\Models\Common\AdminService;
use App\Models\Common\Card;
use App\Models\Common\CompanyCity;
use App\Models\Common\CompanyCountry;
use App\Models\Common\Menu;
use App\Models\Common\PaymentLog;
use App\Models\Common\Promocode;
use App\Models\Common\Provider;
use App\Models\Common\Rating;
use App\Models\Common\Reason;
use App\Models\Common\ServiceArea;
use App\Models\Common\Setting;
use App\Models\Common\User;
use App\Models\Common\UserRequest;
use App\Models\Service\Service;
use App\Models\Service\ServiceCancelProvider;
use App\Models\Service\ServiceCategory;
use App\Models\Service\ServiceCityPrice;
use App\Models\Service\ServiceRequest;
use App\Models\Service\ServiceRequestDispute;
use App\Models\Service\ServiceRequestPayment;
use App\Models\Service\ServiceSubCategory;
use App\Services\PaymentGateway;
use App\Services\SendPushNotification;
use App\Services\V1\Common\UserServices;
use App\Services\V1\Service\Services;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function add_cancel_request(Request $request)
    {
        Log::info($request->all());
        try {
            $serviceRequest = 0;

            $user_request = new UserRequest();
            $user_request->user_id = $request->user_id;
            $user_request->provider_id = $request->provider_id;
            $user_request->schedule_at = $request->schedule_at;
            $user_request->company_id = $request->company_id;
            $user_request->admin_service = "SERVICE";
            $user_request->status = $request->status;
            $user_request->s_latitude = $request->s_latitude;
            $user_request->s_longitude = $request->s_longitude;
            $user_request->s_address = $request->s_address;
            $user_request->project_name = $request->project_name;
            $user_request->pin_code = $request->pin_code;
            $user_request->this_kind_location = $request->this_kind_location;
            $user_request->emergency = $request->emergency;
            $user_request->payment_mode = $request->payment_mode;
            $user_request->service_id = $request->service_id;

            $user_request->request_data = json_encode(@$serviceRequest);
            $user_request->save();

            return Helper::getResponse(["data" => $user_request]);
        } catch (\Throwable $e) {
            Log::info($e);
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function providerServiceListZipCode(Request $request)
    {
        $settings = json_decode(
            json_encode(
                Setting::where(
                    "company_id",
                    Auth::guard("user")->user()->company_id
                )->first()->settings_data
            )
        );

        $siteConfig = $settings->site;
        $serviceConfig = $settings->service;

        $distance = $serviceConfig->provider_search_radius
            ? $serviceConfig->provider_search_radius
            : 100;

        $latitude = $request->lat;
        $longitude = $request->long;
        $service_id = $request->id;

        $admin_service = AdminService::where("admin_service", "SERVICE")
            ->where("company_id", Auth::guard("user")->user()->company_id)
            ->first();

        $currency = CompanyCountry::where(
            "company_id",
            Auth::guard("user")->user()->company_id
        )
            ->where("country_id", Auth::guard("user")->user()->country_id)
            ->first();
        if (!empty($request->zipcode)) {
            $service_cancel_provider = ServiceCancelProvider::select(
                "id",
                "provider_id"
            )
                ->where("company_id", Auth::guard("user")->user()->company_id)
                ->where("user_id", Auth::guard("user")->user()->id)
                ->pluck("provider_id", "provider_id")
                ->toArray();

            $admin_id = $admin_service->id;

            $provider_service_init = Provider::where(
                "zipcode",
                $request->zipcode
            )
                ->where("status", "approved")
                ->where("is_online", 1)
                ->where("is_assigned", 0)
                ->whereDoesntHave("request_filter")
                ->whereHas("service", function ($q) use (
                    $admin_id,
                    $service_id
                ) {
                    $q->where("admin_service", "SERVICE");
                    $q->where("service_id", $service_id);
                });

            $provider_service_init->whereNotIn("id", $service_cancel_provider);
            $provider_service = $provider_service_init->get();

            if ($provider_service) {
                $providers = [];
                if (!empty($provider_service[0]->service)) {
                    $serviceDetails = Service::with("serviceCategory")
                        ->where("id", $service_id)
                        ->where(
                            "company_id",
                            Auth::guard("user")->user()->company_id
                        )
                        ->first();
                    foreach ($provider_service as $key => $service) {
                        unset($service->request_filter);
                        $provider = new \stdClass();
                        $provider->distance = $service->distance;
                        $provider->id = $service->id;
                        $provider->first_name = $service->first_name;
                        $provider->last_name = $service->last_name;
                        $provider->picture = $service->picture;
                        $provider->rating = $service->rating;
                        $provider->city_id = $service->city_id;
                        $provider->latitude = $service->latitude;
                        $provider->longitude = $service->longitude;
                        if ($service->service_city == null) {
                            $provider->fare_type = "FIXED";
                            $provider->base_fare = "0";
                            $provider->per_miles = "0";
                            $provider->per_mins = "0";
                            $provider->price_choose = "";
                        } else {
                            $provider->fare_type =
                                $service->service_city->fare_type;
                            if (
                                $serviceDetails->serviceCategory
                                ->price_choose == "admin_price"
                            ) {
                                if (!empty($request->qty)) {
                                    $provider->base_fare = Helper::decimalRoundOff(
                                        $service->service_city->base_fare *
                                            $request->qty
                                    );
                                } else {
                                    $provider->base_fare = Helper::decimalRoundOff(
                                        $service->service_city->base_fare
                                    );
                                }

                                $provider->per_miles = Helper::decimalRoundOff(
                                    $service->service_city->per_miles
                                );
                                $provider->per_mins = Helper::decimalRoundOff(
                                    $service->service_city->per_mins * 60
                                );
                            } else {
                                if (!empty($request->qty)) {
                                    $provider->base_fare = Helper::decimalRoundOff(
                                        $service->service->base_fare *
                                            $request->qty
                                    );
                                } else {
                                    $provider->base_fare = Helper::decimalRoundOff(
                                        $service->service->base_fare
                                    );
                                }

                                $provider->per_miles = Helper::decimalRoundOff(
                                    $service->service->per_miles
                                );
                                $provider->per_mins = Helper::decimalRoundOff(
                                    $service->service->per_mins * 60
                                );
                            }

                            $provider->price_choose =
                                $serviceDetails->serviceCategory->price_choose;
                        }

                        $providers[] = $provider;
                    }
                }

                return Helper::getResponse([
                    "data" => [
                        "provider_service" => $providers,
                        "currency" =>
                        $currency != null ? $currency->currency : "",
                    ],
                ]);
            }
        } else {
            $providers = [];

            return Helper::getResponse([
                "data" => [
                    "provider_service" => $providers,
                    "currency" => $currency != null ? $currency->currency : "",
                ],
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function providerServiceList(Request $request)
    {
        $settings = json_decode(
            json_encode(
                Setting::where(
                    "company_id",
                    Auth::guard("user")->user()->company_id
                )->first()->settings_data
            )
        );

        $siteConfig = $settings->site;
        $serviceConfig = $settings->service;

        $distance = $serviceConfig->provider_search_radius
            ? $serviceConfig->provider_search_radius
            : 100;

        $latitude = $request->lat;
        $longitude = $request->long;
        $service_id = $request->id;

        $geofence = (new UserServices())->poly_check_request(
            round($latitude, 6),
            round($longitude, 6),
            Auth::guard("user")->user()->city_id
        );

        $admin_service = AdminService::where("admin_service", "SERVICE")
            ->where("company_id", Auth::guard("user")->user()->company_id)
            ->first();

        $currency = CompanyCountry::where(
            "company_id",
            Auth::guard("user")->user()->company_id
        )
            ->where("country_id", Auth::guard("user")->user()->country_id)
            ->first();
        if ($geofence) {
            $service_cancel_provider = ServiceCancelProvider::select(
                "id",
                "provider_id"
            )
                ->where("company_id", Auth::guard("user")->user()->company_id)
                ->where("user_id", Auth::guard("user")->user()->id)
                ->pluck("provider_id", "provider_id")
                ->toArray();

            $admin_id = $admin_service->id;
            $callback = function ($q) use ($admin_id, $service_id) {
                $q->where("admin_service", "SERVICE");
                $q->where("service_id", $service_id);
            };

            $provider_service_init = Provider::with([
                "service" => $callback,
                "service_city" => function ($q) use ($service_id) {
                    return $q->where("service_id", $service_id);
                },
                "request_filter",
            ])
                ->select(
                    DB::Raw(
                        "(3959 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) AS distance"
                    ),
                    "id",
                    "first_name",
                    "last_name",
                    "picture",
                    "city_id",
                    "rating",
                    "latitude",
                    "longitude"
                )
                ->where("status", "approved")
                ->where("is_online", 1)
                ->where("is_assigned", 0)
                ->where("company_id", Auth::guard("user")->user()->company_id)
                ->where("city_id", Auth::guard("user")->user()->city_id)
                ->whereRaw(
                    "(3959 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance"
                )
                ->whereDoesntHave("request_filter")
                ->whereHas("service", function ($q) use (
                    $admin_id,
                    $service_id
                ) {
                    $q->where("admin_service", "SERVICE");
                    $q->where("service_id", $service_id);
                })
                ->where(
                    "wallet_balance",
                    ">=",
                    $siteConfig->provider_negative_balance
                );

            $provider_service_init->orderBy("distance", "asc");

            $provider_service_init->whereNotIn("id", $service_cancel_provider);
            $provider_service = $provider_service_init->get();

            if ($provider_service) {
                $providers = [];
                if (!empty($provider_service[0]->service)) {
                    $serviceDetails = Service::with("serviceCategory")
                        ->where("id", $service_id)
                        ->where(
                            "company_id",
                            Auth::guard("user")->user()->company_id
                        )
                        ->first();
                    foreach ($provider_service as $key => $service) {
                        unset($service->request_filter);
                        $provider = new \stdClass();
                        $provider->distance = $service->distance;
                        $provider->id = $service->id;
                        $provider->first_name = $service->first_name;
                        $provider->last_name = $service->last_name;
                        $provider->picture = $service->picture;
                        $provider->rating = $service->rating;
                        $provider->city_id = $service->city_id;
                        $provider->latitude = $service->latitude;
                        $provider->longitude = $service->longitude;
                        if ($service->service_city == null) {
                            $provider->fare_type = "FIXED";
                            $provider->base_fare = "0";
                            $provider->per_miles = "0";
                            $provider->per_mins = "0";
                            $provider->price_choose = "";
                        } else {
                            $provider->fare_type =
                                $service->service_city->fare_type;
                            if (
                                $serviceDetails->serviceCategory
                                ->price_choose == "admin_price"
                            ) {
                                if (!empty($request->qty)) {
                                    $provider->base_fare = Helper::decimalRoundOff(
                                        $service->service_city->base_fare *
                                            $request->qty
                                    );
                                } else {
                                    $provider->base_fare = Helper::decimalRoundOff(
                                        $service->service_city->base_fare
                                    );
                                }

                                $provider->per_miles = Helper::decimalRoundOff(
                                    $service->service_city->per_miles
                                );
                                $provider->per_mins = Helper::decimalRoundOff(
                                    $service->service_city->per_mins * 60
                                );
                            } else {
                                if (!empty($request->qty)) {
                                    $provider->base_fare = Helper::decimalRoundOff(
                                        $service->service->base_fare *
                                            $request->qty
                                    );
                                } else {
                                    $provider->base_fare = Helper::decimalRoundOff(
                                        $service->service->base_fare
                                    );
                                }

                                $provider->per_miles = Helper::decimalRoundOff(
                                    $service->service->per_miles
                                );
                                $provider->per_mins = Helper::decimalRoundOff(
                                    $service->service->per_mins * 60
                                );
                            }

                            $provider->price_choose =
                                $serviceDetails->serviceCategory->price_choose;
                        }

                        $providers[] = $provider;
                    }
                }

                return Helper::getResponse([
                    "data" => [
                        "provider_service" => $providers,
                        "currency" =>
                        $currency != null ? $currency->currency : "",
                    ],
                ]);
            }
        } else {
            $providers = [];

            return Helper::getResponse([
                "data" => [
                    "provider_service" => $providers,
                    "currency" => $currency != null ? $currency->currency : "",
                ],
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function review(Request $request, $id)
    {
        if ($request->has("limit")) {
            $review = Rating::select(
                "id",
                "admin_service",
                "user_id",
                "provider_id",
                "provider_rating",
                "provider_comment",
                "user_comment",
                "user_rating",
                "created_at"
            )
                ->where("provider_id", $id)
                ->where([
                    "company_id" => Auth::guard("user")->user()->company_id,
                ])
                ->where("admin_service", "SERVICE")
                ->whereNotNull("user_comment")
                ->where("user_comment", "!=", "")
                ->with([
                    "user" => function ($query) {
                        $query->select(
                            "id",
                            "first_name",
                            "last_name",
                            "picture"
                        );
                    },
                ])
                ->take($request->limit)
                ->offset($request->offset)
                ->orderby("id", "desc")
                ->get();
        } else {
            $review = Rating::select(
                "id",
                "admin_service",
                "user_id",
                "provider_id",
                "provider_rating",
                "provider_comment",
                "user_comment",
                "user_rating",
                "created_at"
            )
                ->where("provider_id", $id)
                ->where([
                    "company_id" => Auth::guard("user")->user()->company_id,
                ])
                ->where("admin_service", "SERVICE")
                ->with([
                    "user" => function ($query) {
                        $query->select(
                            "id",
                            "first_name",
                            "last_name",
                            "picture"
                        );
                    },
                ])
                ->orderby("id", "desc")
                ->get();
        }
        $jsonResponse["total_records"] = count($review);
        $jsonResponse["review"] = $review;
        if ($jsonResponse) {
            return Helper::getResponse(["data" => $jsonResponse]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function service(Request $request, $id)
    {
        $service = Service::where("id", $id)
            ->where(["company_id" => Auth::guard("user")->user()->company_id])
            ->first();
        if ($service) {
            return Helper::getResponse(["data" => $service]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function cancel_request(Request $request, $id)
    {
        try {
            $service_cancel_provider = new ServiceCancelProvider;
            $service_cancel_provider->company_id = Auth::guard('user')->user()->company_id;;
            $service_cancel_provider->user_id = Auth::guard('user')->user()->id;;
            $service_cancel_provider->provider_id = $id;
            $service_cancel_provider->service_id = 1;
            $service_cancel_provider->save();
            return Helper::getResponse(['message' => trans('Cancel the Provider request')]);
        } catch (\Throwable $e) {
            return Helper::getResponse(['status' => 500, 'message' => trans('api.ride.request_not_completed'), 'error' => $e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource.
     * @throws ValidationException
     */
    public function create_service(Request $request)
    {
        $this->validate($request, [
            "service_id" => "required|integer|exists:service.services,id",
            "s_latitude" => "required",
            "s_longitude" => "required",
            "payment_mode" => "required",
        ]);

        try {
            $service = (new Services())->create_service($request);
            return Helper::getResponse([
                "status" => $service["status"] ?? 200,
                "message" => $service["message"] ? $service["message"] : "",
                "data" => $service["data"] ?? [],
            ]);
        } catch (Exception $e) {
            return Helper::getResponse([
                "status" => 500,
                "message" => trans("api.service.request_not_completed"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function status(Request $request)
    {
        try {
            $settings = json_decode(
                json_encode(
                    Setting::where(
                        "company_id",
                        Auth::guard("user")->user()->company_id
                    )->first()->settings_data
                )
            );

            $siteConfig = $settings->site;

            $serviceConfig = $settings->service;

            $check_status = ["CANCELLED", "SCHEDULED"];
            $serviceRequest = ServiceRequest::ServiceRequestStatusCheck(
                Auth::guard("user")->user()->id,
                $check_status
            )->get();

            $search_status = ["SEARCHING", "SCHEDULED"];
            $serviceRequestFilter = ServiceRequest::ServiceRequestAssignProvider(
                Auth::guard("user")->user()->id,
                $search_status
            )->get();
            $Timeout = $serviceConfig->provider_select_timeout
                ? $serviceConfig->provider_select_timeout
                : 60;
            $response_time = $Timeout;
            if (!empty($serviceRequest)) {
                // $serviceRequest[0]['ride_otp'] = (int) $serviceConfig->serve_otp ? $serviceConfig->serve_otp : 0 ;

                // $serviceRequest[0]['reasons']=Reason::where('type','USER')->get();
                // $categoryId = $serviceRequest[0]['service']['service_category_id'];
                foreach ($serviceRequest as $key => $requestlist) {
                    $categoryId = $requestlist->service->service_category_id;
                    $subCategoryId =
                        $requestlist->service->service_subcategory_id;
                    $requestlist->category = ServiceCategory::where(
                        "id",
                        $categoryId
                    )->first();
                    $requestlist->subcategory = ServiceSubCategory::where(
                        "id",
                        $subCategoryId
                    )->first();
                    $requestlist->reasons = Reason::where(
                        "type",
                        "USER"
                    )->get();
                    $response_time =
                        $Timeout -
                        (time() -
                            strtotime($serviceRequest[$key]->assigned_at));
                }
            }

            if (empty($serviceRequest)) {
                $cancelled_request = ServiceRequest::where(
                    "service_requests.user_id",
                    Auth::guard("user")->user()->id
                )
                    ->where("service_requests.user_rated", 0)
                    ->where("service_requests.status", ["CANCELLED"])
                    ->orderby("updated_at", "desc")
                    ->where(
                        "updated_at",
                        ">=",
                        \Carbon\Carbon::now()->subSeconds(5)
                    )
                    ->first();
            }
            return Helper::getResponse([
                "data" => [
                    "response_time" => $response_time,
                    "data" => $serviceRequest,
                    "sos" => $siteConfig->sos_number ?? "911",
                    "emergency" => $siteConfig->contact_number ?? [["number" => "911"]],
                ],
            ]);
        } catch (Exception $e) {
            return Helper::getResponse([
                "status" => 500,
                "message" => trans("api.something_went_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function checkService(Request $request, $id)
    {
        try {
            $settings = json_decode(
                json_encode(
                    Setting::where(
                        "company_id",
                        Auth::guard("user")->user()->company_id
                    )->first()->settings_data
                )
            );

            $siteConfig = $settings->site;

            $serviceConfig = $settings->service;

            $check_status = ["CANCELLED", "SCHEDULED"];
            $serviceRequest = ServiceRequest::ServiceRequestStatusCheck(
                Auth::guard("user")->user()->id,
                $check_status
            )
                ->where("id", $id)
                ->get();

            $search_status = ["SEARCHING", "SCHEDULED"];
            $serviceRequestFilter = ServiceRequest::ServiceRequestAssignProvider(
                Auth::guard("user")->user()->id,
                $search_status
            )->get();
            $Timeout = $serviceConfig->provider_select_timeout
                ? $serviceConfig->provider_select_timeout
                : 60;
            $response_time = $Timeout;
            if (!empty($serviceRequest)) {
                // $serviceRequest[0]['ride_otp'] = (int) $serviceConfig->serve_otp ? $serviceConfig->serve_otp : 0 ;

                // $serviceRequest[0]['reasons']=Reason::where('type','USER')->get();
                // $categoryId = $serviceRequest[0]['service']['service_category_id'];
                foreach ($serviceRequest as $key => $requestlist) {
                    $categoryId = $requestlist->service->service_category_id;
                    $subCategoryId =
                        $requestlist->service->service_subcategory_id;
                    $requestlist->category = ServiceCategory::where(
                        "id",
                        $categoryId
                    )->first();
                    $requestlist->subcategory = ServiceSubCategory::where(
                        "id",
                        $subCategoryId
                    )->first();
                    $requestlist->reasons = Reason::where(
                        "type",
                        "USER"
                    )->get();
                    $response_time =
                        $Timeout -
                        (time() -
                            strtotime($requestlist->assigned_at));
                }
            }

            if (empty($serviceRequest)) {
                $cancelled_request = ServiceRequest::where(
                    "service_requests.user_id",
                    Auth::guard("user")->user()->id
                )
                    ->where("service_requests.user_rated", 0)
                    ->where("service_requests.status", ["CANCELLED"])
                    ->orderby("updated_at", "desc")
                    ->where(
                        "updated_at",
                        ">=",
                        \Carbon\Carbon::now()->subSeconds(5)
                    )
                    ->first();
            }
            return Helper::getResponse([
                "data" => [
                    "response_time" => $response_time,
                    "data" => $serviceRequest,
                    "sos" => $siteConfig->sos_number ?? "911",
                    "emergency" => $siteConfig->contact_number ?? [["number" => "911"]],
                ],
            ]);
        } catch (Exception $e) {
            return Helper::getResponse([
                "status" => 500,
                "message" => trans("api.something_went_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function cancel_service(Request $request)
    {
        $this->validate($request, [
            "id" =>
            "required|numeric|exists:service.service_requests,id,user_id," .
                Auth::guard("user")->user()->id,
        ]);

        $request->request->add(["cancelled_by" => "USER"]);

        try {
            $service = (new Services())->cancelService($request);
            return Helper::getResponse([
                "status" => $service["status"] ?? 200,
                "message" => $service["message"] ? $service["message"] : "",
                "data" => $service["data"] ?? [],
            ]);
        } catch (Exception $e) {
            return Helper::getResponse([
                "status" => 500,
                "message" => trans("api.service.request_not_completed"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function rate(Request $request)
    {
        $this->validate(
            $request,
            [
                "rating" => "required",
                "comment" => "max:255",
            ],
            ["comment.max" => "character limit should not exceed 255"]
        );

        $serviceRequest = ServiceRequest::findOrFail($request->id);
        if ($serviceRequest->paid == 0) {
            return Helper::getResponse([
                "status" => 422,
                "message" => trans("api.user.not_paid"),
                "error" => trans("api.user.not_paid"),
            ]);
        }
        try {
            $admin_service = AdminService::where("admin_service", "SERVICE")
                ->where("company_id", Auth::guard("user")->user()->company_id)
                ->first();

            $serviceRequest = ServiceRequest::findOrFail($request->id);

            $ratingRequest = Rating::where("request_id", $serviceRequest->id)
                ->where("admin_service", "SERVICE")
                ->first();

            if ($ratingRequest == null) {
                $request->request->add([
                    "company_id" => $serviceRequest->company_id,
                ]);
                $request->request->add([
                    "provider_id" => $serviceRequest->provider_id,
                ]);
                $request->request->add(["user_id" => $serviceRequest->user_id]);
                $request->request->add(["request_id" => $serviceRequest->id]);
                (new CommonController())->rating(
                    $request
                );
            } else {
                $serviceRequest->rating->update([
                    "user_rating" => $request->rating,
                    "user_comment" => $request->comment,
                ]);
            }
            $serviceRequest->user_rated = 1;
            $serviceRequest->save();

            $average = Rating::where(
                "provider_id",
                $serviceRequest->provider_id
            )->avg("user_rating");

            $User = User::find($serviceRequest->user_id);
            $User->rating = $average;
            $User->save();

            // Send Push Notification to Provider
            return Helper::getResponse([
                "message" => trans("api.service.service_rated"),
            ]);
        } catch (Exception $e) {
            return Helper::getResponse([
                "status" => 500,
                "message" => trans("api.ride.request_completed"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function payment(Request $request)
    {
        try {
            $tip_amount = 0;

            $serviceRequest = ServiceRequest::find(
                $request->id
            );
            $payment = ServiceRequestPayment::where(
                "service_request_id",
                $request->id
            )->first();

            $user = User::find($serviceRequest->user_id);
            $setting = Setting::where("company_id", $user->company_id)->first();
            $settings = json_decode(json_encode($setting->settings_data));
            $siteConfig = $settings->site;
            $serviceConfig = $settings->service;
            $paymentConfig = json_decode(json_encode($settings->payment), true);

            $cardObject = array_values(
                array_filter($paymentConfig, function ($e) {
                    return $e["name"] == "card";
                })
            );
            $card = 0;

            $stripe_secret_key = "";
            $stripe_publishable_key = "";
            $stripe_currency = "";

            if (count($cardObject) > 0) {
                $card = $cardObject[0]["status"];

                $stripeSecretObject = array_values(
                    array_filter($cardObject[0]["credentials"], function ($e) {
                        return $e["name"] == "stripe_secret_key";
                    })
                );
                $stripePublishableObject = array_values(
                    array_filter($cardObject[0]["credentials"], function ($e) {
                        return $e["name"] == "stripe_publishable_key";
                    })
                );
                $stripeCurrencyObject = array_values(
                    array_filter($cardObject[0]["credentials"], function ($e) {
                        return $e["name"] == "stripe_currency";
                    })
                );

                if (count($stripeSecretObject) > 0) {
                    $stripe_secret_key = $stripeSecretObject[0]["value"];
                }

                if (count($stripePublishableObject) > 0) {
                    $stripe_publishable_key =
                        $stripePublishableObject[0]["value"];
                }

                if (count($stripeCurrencyObject) > 0) {
                    $stripe_currency = $stripeCurrencyObject[0]["value"];
                }
            }

            $random = $serviceConfig->booking_prefix . mt_rand(100000, 999999);

            if (isset($request->tips) && !empty($request->tips)) {
                $tip_amount = round($request->tips, 2);
            }

            $totalAmount = $payment->payable + $tip_amount;

            $paymentMode = $request->has("payment_mode")
                ? strtoupper($request->payment_mode)
                : $serviceRequest->payment_mode;
            if ($request->payment_mode != "CASH") {
                if ($totalAmount == 0) {
                    $serviceRequest->payment_mode = $paymentMode;
                    $payment->card = $payment->payable;
                    $payment->payable = 0;
                    $payment->tips = $tip_amount;
                    $payment->provider_pay =
                        $payment->provider_pay + $tip_amount;
                    $payment->save();

                    $serviceRequest->paid = 1;
                    $serviceRequest->status = "COMPLETED";
                    $serviceRequest->save();

                    $requestData = [
                        "type" => "SERVICE",
                        "room" => "room_" . $serviceRequest->company_id,
                        "id" => $serviceRequest->id,
                        "city" =>
                        $setting->demo_mode == 0
                            ? $serviceRequest->city_id
                            : 0,
                        "user" => $serviceRequest->user_id,
                    ];
                    app("redis")->publish(
                        "checkServiceRequest",
                        json_encode($requestData)
                    );

                    return Helper::getResponse([
                        "message" => trans("api.paid"),
                    ]);
                } else {
                    $log = new PaymentLog();
                    $log->admin_service = "SERVICE";
                    $log->company_id = $user->company_id;
                    $log->user_type = "user";
                    $log->transaction_code = $random;
                    $log->amount = $totalAmount;
                    $log->transaction_id = $serviceRequest->id;
                    $log->payment_mode = $paymentMode;
                    $log->user_id = $serviceRequest->user_id;
                    $log->save();
                    switch ($paymentMode) {
                        case "CARD":
                            $card = Card::where(
                                "user_id",
                                $serviceRequest->user_id
                            )
                                ->where("is_default", 1)
                                ->first();

                            if ($card == null) {
                                $card = Card::where(
                                    "user_id",
                                    $serviceRequest->user_id
                                )->first();
                            }
                            if ($card == null) {
                                return Helper::getResponse([
                                    "status" => 500,
                                    "message" => trans("api.add_card_required"),
                                ]);
                            }

                            $gateway = new PaymentGateway("stripe");

                            $response = $gateway->process([
                                "order" => $random,
                                "amount" => $totalAmount,
                                "currency" => $stripe_currency,
                                "customer" => $user->stripe_cust_id,
                                "card" => $card->card_id,
                                "description" =>
                                "Payment Charge for " . $user->email,
                                "receipt_email" => $user->email,
                            ]);

                            break;
                    }
                    if ($response->status == "SUCCESS") {
                        $payment->payment_id = $response->payment_id;
                        $payment->payment_mode = $paymentMode;
                        $payment->card = $payment->payable;
                        $payment->payable = 0;
                        $payment->tips = $tip_amount;
                        $payment->total = $totalAmount;
                        $payment->provider_pay =
                            $payment->provider_pay + $tip_amount;
                        $payment->save();

                        $serviceRequest->paid = 1;
                        $serviceRequest->status = "COMPLETED";
                        $serviceRequest->save();

                        //for create the transaction
                        (new ProviderServiceController())->callTransaction(
                            $serviceRequest->id
                        );
                        $requestData = [
                            "type" => "SERVICE",
                            "room" => "room_" . $serviceRequest->company_id,
                            "id" => $serviceRequest->id,
                            "city" =>
                            $setting->demo_mode == 0
                                ? $serviceRequest->city_id
                                : 0,
                            "user" => $serviceRequest->user_id,
                        ];
                        app("redis")->publish(
                            "checkServiceRequest",
                            json_encode($requestData)
                        );

                        return Helper::getResponse([
                            "message" => trans("api.paid"),
                        ]);
                    } else {
                        return Helper::getResponse([
                            "message" => trans("Transaction Failed"),
                        ]);
                    }
                }
            } else {
                $serviceRequest->paid = 1;
                $serviceRequest->save();
                $requestData = [
                    "type" => "SERVICE",
                    "room" => "room_" . $serviceRequest->company_id,
                    "id" => $serviceRequest->id,
                    "city" =>
                    $setting->demo_mode == 0 ? $serviceRequest->city_id : 0,
                    "user" => $serviceRequest->user_id,
                ];
                app("redis")->publish(
                    "checkServiceRequest",
                    json_encode($requestData)
                );
            }
        } catch (\Throwable $e) {
            return Helper::getResponse([
                "status" => 500,
                "message" => trans("api.ride.request_not_completed"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function update_payment_method(Request $request)
    {
        $this->validate($request, [
            "id" => "required|exists:service.service_requests",
            "payment_mode" => "required",
        ]);

        try {
            if ($request->has("card_id")) {
                Card::where("user_id", Auth::guard("user")->user()->id)->update(
                    ["is_default" => 0]
                );
                Card::where("card_id", $request->card_id)->update([
                    "is_default" => 1,
                ]);
            }

            $serviceRequest = ServiceRequest::findOrFail($request->id);
            $serviceRequest->payment_mode = $request->payment_mode;

            if ($request->payment_mode != "CASH") {
                $serviceRequest->status = "DROPPED";
                $serviceRequest->save();
            }

            $serviceRequest->save();

            $payment = ServiceRequestPayment::where(
                "service_request_id",
                $serviceRequest->id
            )->first();

            if ($payment != null) {
                $payment->payment_mode = $request->payment_mode;
                $payment->save();
            }

            $admin_service = AdminService::where("admin_service", "SERVICE")
                ->where("company_id", Auth::guard("user")->user()->company_id)
                ->first();

            $user_request = UserRequest::where("request_id", $request->id)
                ->where("admin_service", "SERVICE")
                ->first();
            $user_request->request_data = json_encode($serviceRequest);
            $user_request->save();

            //Send message to socket
            $requestData = [
                "type" => $user_request->admin_service,
                "id" => $request->id,
                "room" => "room_" . Auth::guard("user")->user()->company_id,
                "payment_mode" => $request->payment_mode,
            ];
            app("redis")->publish("paymentUpdate", json_encode($requestData));

            (new SendPushNotification())->updateProviderStatus(
                $user_request->provider_id,
                "provider",
                trans("api.service.payment_updated"),
                "Payment Mode Changed",
                ""
            );

            return Helper::getResponse([
                "message" => trans("api.service.payment_updated"),
            ]);
        } catch (ModelNotFoundException $e) {
            return Helper::getResponse([
                "status" => 500,
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function promocode(Request $request)
    {
        $promocodes = Promocode::where(
            "company_id",
            Auth::guard("user")->user()->company_id
        )
            ->where("service", "SERVICE")
            ->where("expiration", ">=", date("Y-m-d H:i"))
            ->whereDoesntHave("promousage", function ($query) {
                $query->where("user_id", Auth::guard("user")->user()->id);
            })
            ->get();

        return Helper::getResponse(["data" => $promocodes]);
    }

    /**
     * Display a listing of the resource.
     */
    public function update_service(Request $request, $id)
    {
        $update_service = Service::where("id", $id)->update([
            "allow_desc" => "0",
        ]);
        return Helper::getResponse(["data" => $update_service]);
    }

    /**
     * Display a listing of the resource.
     */
    public function servicearea(Request $request)
    {
        Log::info($request->all());
        try {
            $servicearea = new ServiceArea();

            $servicearea->provider_id = $request->provider_id;
            $servicearea->type = $request->type;
            $servicearea->miles = $request->miles;
            $servicearea->save();
            Log::info("serviceareaaaaaa saveeeeeeee");
            Log::info($servicearea);
            return Helper::getResponse(["data" => $servicearea]);
        } catch (\Throwable $e) {
            Log::info($e);
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function servicearea_list(Request $request)
    {
        try {
            $servicearea_list = ServiceArea::where(
                "provider_id",
                Auth::guard("provider")->user()->id
            )
                ->orderBy("id", "desc")
                ->get();
            return Helper::getResponse(["data" => $servicearea_list]);
        } catch (\Throwable $e) {
            Log::info($e);
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function requestHistory(Request $request)
    {
        try {
            $history_status = ["CANCELLED", "COMPLETED"];
            $datum = ServiceRequest::where(
                "company_id",
                Auth::user()->company_id
            )
                ->whereIn("status", $history_status)
                ->with("payment", "user", "provider", "rating");
            if (Auth::user()->hasRole("FLEET")) {
                $datum->where("admin_id", Auth::user()->id);
            }
            if ($request->has("search_text") && $request->search_text != null) {
                $datum->Search($request->search_text);
            }
            if ($request->has("order_by")) {
                $datum->orderby($request->order_by, $request->order_direction);
            }
            $data = $datum->paginate(10);
            return Helper::getResponse(["data" => $data]);
        } catch (\Throwable $e) {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function requestScheduleHistory(Request $request)
    {
        try {
            $scheduled_status = ["SCHEDULED"];
            $datum = ServiceRequest::where(
                "company_id",
                Auth::user()->company_id
            )
                ->whereIn("status", $scheduled_status)
                ->with("user", "provider");
            if (Auth::user()->hasRole("FLEET")) {
                $datum->where("admin_id", Auth::user()->id);
            }
            if ($request->has("search_text") && $request->search_text != null) {
                $datum->Search($request->search_text);
            }
            if ($request->has("order_by")) {
                $datum->orderby($request->order_by, $request->order_direction);
            }
            $data = $datum->paginate(10);
            return Helper::getResponse(["data" => $data]);
        } catch (\Throwable $e) {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function requestHistoryDetails($id)
    {
        try {
            $data = ServiceRequest::with(
                "user",
                "provider",
                "rating",
                "service",
                "serviceCategory"
            )->findOrFail($id);
            return Helper::getResponse(["data" => $data]);
        } catch (\Throwable $e) {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function webproviderservice(Request $request, $id)
    {
        try {
            $storetype = Service::with([
                "provideradminservice" => function ($query) use ($id) {
                    $query->where("provider_id", $id);
                },
            ])
                ->with("serviceCategory", "servicesubCategory")
                ->where("company_id", Auth::user()->company_id)
                ->get();

            return Helper::getResponse(["data" => $storetype]);
        } catch (ModelNotFoundException $e) {
            return Helper::getResponse([
                "status" => 500,
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function searchServiceDispute(Request $request)
    {
        $results = [];
        $term = $request->input("stext");
        if ($request->input("sflag") == 1) {
            $queries = ServiceRequest::where("provider_id", $request->id)
                ->with("service")
                ->orderby("id", "desc")
                ->take(10)
                ->get();
        } else {
            $queries = ServiceRequest::where("user_id", $request->id)
                ->with("service")
                ->orderby("id", "desc")
                ->take(10)
                ->get();
        }
        foreach ($queries as $query) {
            $RequestDispute = ServiceRequestDispute::where(
                "service_request_id",
                $query->id
            )->first();
            if (!$RequestDispute) {
                $results[] = $query;
            }
        }
        return response()->json(["success" => true, "data" => $results]);
    }

    /**
     * Display a listing of the resource.
     */
    public function requestStatementHistory(Request $request)
    {
        try {
            $history_status = ["CANCELLED", "COMPLETED"];
            $serviceRequests = ServiceRequest::where(
                "company_id",
                Auth::user()->company_id
            )->with("user", "provider", "service.serviceCategory");

            if (Auth::user()->hasRole("FLEET")) {
                $serviceRequests->where("admin_id", Auth::user()->id);
            }
            if ($request->has("search_text") && $request->search_text != null) {
                $serviceRequests->Search($request->search_text);
            }
            if ($request->has("status") && $request->status != null) {
                $history_status = [$request->status];
            }

            if ($request->has("user_id") && $request->user_id != null) {
                $serviceRequests->where("user_id", $request->user_id);
            }

            if ($request->has("provider_id") && $request->provider_id != null) {
                $serviceRequests->where("provider_id", $request->provider_id);
            }

            if ($request->has("ride_type") && $request->ride_type != null) {
                $serviceRequests->whereHas("service.serviceCategory", function (
                    $q
                ) use ($request) {
                    return $q->where("id", $request->ride_type);
                });
            }

            if ($request->has("order_by")) {
                $serviceRequests->orderby(
                    $request->order_by,
                    $request->order_direction
                );
            }
            $type = isset($_GET["type"]) ? $_GET["type"] : "";
            if ($type == "today") {
                $serviceRequests->where("created_at", ">=", Carbon::today());
            } elseif ($type == "monthly") {
                $serviceRequests->where(
                    "created_at",
                    ">=",
                    Carbon::now()->month
                );
            } elseif ($type == "yearly") {
                $serviceRequests->where(
                    "created_at",
                    ">=",
                    Carbon::now()->year
                );
            } elseif ($type == "range") {
                if ($request->has("from") && $request->has("to")) {
                    if ($request->from == $request->to) {
                        $serviceRequests->whereDate(
                            "created_at",
                            date("Y-m-d", strtotime($request->from))
                        );
                    } else {
                        $serviceRequests->whereBetween("created_at", [
                            Carbon::createFromFormat("Y-m-d", $request->from),
                            Carbon::createFromFormat("Y-m-d", $request->to),
                        ]);
                    }
                }
            }
            $cancelservices = $serviceRequests;
            $orderCounts = $serviceRequests->count();
            $dataval = $serviceRequests
                ->whereIn("status", $history_status)
                ->paginate(10);
            $cancelledQuery = $cancelservices
                ->where("status", "CANCELLED")
                ->count();
            $total_earnings = 0;

            foreach ($dataval as $service) {
                $serviceid = $service->id;
                $earnings = ServiceRequestPayment::select("total")
                    ->where("service_request_id", $serviceid)
                    ->where("company_id", Auth::user()->company_id)
                    ->first();
                if ($earnings != null) {
                    $service->earnings = $earnings->total;
                    $total_earnings = $total_earnings + $earnings->total;
                } else {
                    $service->earnings = 0;
                }
            }
            $data["services"] = $dataval;
            $data["total_services"] = $orderCounts;
            $data["revenue_value"] = round($total_earnings);
            $data["cancelled_services"] = $cancelledQuery;
            return Helper::getResponse(["data" => $data]);
        } catch (\Throwable $e) {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function getcity(Request $request)
    {
        $menudetails = Menu::select("menu_type_id")
            ->where("id", $request->menu_id)
            ->first();

        $serviceprice = ServiceCityPrice::select("city_id")
            ->whereHas("service", function ($query) use ($menudetails) {
                $query->where(
                    "service_category_id",
                    $menudetails->menu_type_id
                );
            })
            ->get()
            ->toArray();
        $company_cities = CompanyCity::with([
            "country",
            "city",
            "menu_city" => function ($query) use ($request) {
                $query->where("menu_id", "=", $request->menu_id);
            },
        ])->where("company_id", Auth::user()->company_id);

        if ($request->has("search_text") && $request->search_text != null) {
            $company_cities = $company_cities->Search($request->search_text);
        }
        $cities = $company_cities->paginate(500);

        foreach ($cities as $key => $value) {
            $cities[$key]["city_price"] = 0;

            if (
                in_array(
                    $value->city_id,
                    array_column($serviceprice, "city_id")
                )
            ) {
                $cities[$key]["city_price"] = 1;
            }
        }

        return Helper::getResponse(["data" => $cities]);
    }
}

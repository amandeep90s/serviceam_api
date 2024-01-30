<?php

namespace App\Http\Controllers\Service\Provider;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\AdminService;
use App\Models\Common\RequestFilter;
use App\Models\Common\UserRequest;
use App\Models\Service\ProjectCategory;
use App\Models\Service\Service;
use App\Models\Service\ServiceCategory;
use App\Models\Service\ServiceSubCategory;
use App\Services\SendPushNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function categories(Request $request)
    {
        try {
            $servicecategory = ServiceCategory::with("providerservicecategory")
                ->where(
                    "company_id",
                    Auth::guard("provider")->user()->company_id
                )
                ->where("service_category_status", 1)
                ->get();
            return Helper::getResponse(["data" => $servicecategory]);
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
    public function subcategories(Request $request)
    {
        $this->validate($request, [
            "service_category_id" => "required",
        ]);

        try {
            $servicesubcategory = ServiceSubCategory::with(
                "providerservicesubcategory"
            )
                ->where(
                    "company_id",
                    Auth::guard("provider")->user()->company_id
                )
                ->where("service_category_id", $request->service_category_id)
                ->where("service_subcategory_status", 1)
                ->get();
            return Helper::getResponse(["data" => $servicesubcategory]);
        } catch (ModelNotFoundException $e) {
            return Helper::getResponse([
                "status" => 500,
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     *  Method to validate
     */
    public function projectcategories(Request $request)
    {
        $this->validate($request, [
            "service_project_category_id" => "required",
        ]);

        try {
            $projectcategory = ProjectCategory::with("providerprojectcategory")
                ->where(
                    "company_id",
                    Auth::guard("provider")->user()->company_id
                )
                ->where(
                    "service_project_category_id",
                    $request->service_project_category_id
                )
                ->where("service_projectcategory_status", 1)
                ->get();
            return Helper::getResponse(["data" => $projectcategory]);
        } catch (ModelNotFoundException $e) {
            Log::info($e);
            return Helper::getResponse([
                "status" => 500,
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function service(Request $request)
    {
        $this->validate($request, [
            "service_category_id" => "required",
            "service_subcategory_id" => "required",
        ]);
        try {
            $servicesubcategory = Service::with([
                "providerservices",
                "service_city" => function ($q) {
                    $q->where(
                        "city_id",
                        Auth::guard("provider")->user()->city_id
                    );
                },
            ])
                ->where(
                    "service_subcategory_id",
                    $request->service_subcategory_id
                )
                ->where("service_category_id", $request->service_category_id)
                ->where("service_status", 1)
                ->get();
            return Helper::getResponse(["data" => $servicesubcategory]);
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
    public function totalservices(Request $request)
    {
        try {
            $category = Service::with([
                "serviceCategory",
                "servicesubCategory",
                "service_city" => function ($q) {
                    $q->where(
                        "city_id",
                        Auth::guard("provider")->user()->city_id
                    );
                },
                "providerservices",
            ])
                ->where(
                    "company_id",
                    Auth::guard("provider")->user()->company_id
                )
                ->where("service_status", 1)
                ->get();

            $data = [];
            foreach ($category as $v) {
                if ($v->serviceCategory) {
                    $category_name = $v->serviceCategory->service_category_name;
                    $category_id = $v->serviceCategory->id;
                    $subcategory =
                        $v->servicesubCategory->service_subcategory_name;
                    $subcategory_id = $v->servicesubCategory->id;
                    $price_choose = $v->serviceCategory->price_choose;
                    $provider_service =
                        !empty($v->providerservices)
                        ? $v->providerservices[0]->id
                        : null;
                    if (!empty($v->providerservices)) {
                        $base_price = $v->providerservices[0]->base_fare;
                        $per_mile = $v->providerservices[0]->per_miles;
                        $per_mins = $v->providerservices[0]->per_mins;
                    } else {
                        $base_price = !empty($v->service_city["base_fare"])
                            ? $v->service_city["base_fare"]
                            : 0.0;
                        $per_mile = !empty($v->service_city["per_miles"])
                            ? $v->service_city["per_miles"]
                            : 0.0;
                        $per_mins = !empty($v->service_city["per_mins"])
                            ? $v->service_city["per_mins"]
                            : 0.0;
                    }
                    $data[$category_name . "-" . $subcategory]["name"][] =
                        $v->service_name;
                    $data[$category_name . "-" . $subcategory]["id"][] = $v->id;
                    $data[$category_name . "-" . $subcategory]["category_id"][] = $category_id;
                    $data[$category_name . "-" . $subcategory]["sub_category_id"][] = $subcategory_id;
                    $data[$category_name . "-" . $subcategory]["price"][] = $base_price;
                    $data[$category_name . "-" . $subcategory]["per_mile"][] = $per_mile;
                    $data[$category_name . "-" . $subcategory]["per_mins"][] = $per_mins;
                    $data[$category_name . "-" . $subcategory]["price_choose"][] = $price_choose;
                    $data[$category_name . "-" . $subcategory]["fare_type"][] = !empty($v->service_city["fare_type"])
                        ? $v->service_city["fare_type"]
                        : "Not Price";
                    $data[$category_name . "-" . $subcategory]["provider_service_id"][] = $provider_service;
                    $data[$category_name . "-" . $subcategory]["currency_symbol"][] = isset(
                        Auth::guard("provider")->user()->currency_symbol
                        )
                        ? Auth::guard("provider")->user()->currency_symbol
                        : "$";
                }
            }
            return Helper::getResponse(["data" => $data]);
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
    public function listtotalservices(Request $request)
    {
        try {
            $category = Service::with([
                "serviceCategory",
                "servicesubCategory",
                "providerservices",
            ])->whereHas("providerservices", function ($q) {
                return $q
                    ->from(env("DB_COMMON_DATABASE") . ".provider_services")
                    ->where("provider_id", Auth::guard("provider")->user()->id);
            });

            if ($request->has("search_text") && $request->search_text != null) {
                $category->historySearch($request->search_text);
            }
            if ($request->has("order_by")) {
                $category->orderby(
                    $request->order_by,
                    $request->order_direction
                );
            }

            if ($request->has("limit")) {
                $data = $category
                    ->where(
                        "company_id",
                        Auth::guard("provider")->user()->company_id
                    )
                    ->take($request->limit)
                    ->offset($request->offset)
                    ->get();
            } elseif ($request->has("flag") && $request->flag == "pagination") {
                $data = $category
                    ->where(
                        "company_id",
                        Auth::guard("provider")->user()->company_id
                    )
                    ->orderby("id", "asc")
                    ->get();
            } else {
                $data = $category
                    ->where(
                        "company_id",
                        Auth::guard("provider")->user()->company_id
                    )
                    ->orderby("id", "asc")
                    ->paginate(10);
            }
            return Helper::getResponse(["data" => $data]);
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
    public function fareTypeServiceList(Request $request)
    {
        $category = Service::with([
            "serviceCategory",
            "servicesubCategory",

            "providerservices",
        ])
            ->where("id", $request->id)
            ->where("company_id", Auth::guard("provider")->user()->company_id)
            ->first();
        if ($category) {
            $category->fareType =
                $category && !empty($category->providerservices[0]->fare_type)
                ? $category->providerservices[0]->fare_type
                : "Not Price";
            $category->currency_symbol = isset(
                Auth::guard("provider")->user()->currency_symbol
                )
                ? Auth::guard("provider")->user()->currency_symbol
                : "$";
            $category->price_choose =
                $category->serviceCategory->price_choose ?? "admin_price";

            $base_price = $per_miles = $per_mins = 0;

            if (
                $category->providerservices &&
                $category->providerservices[0]->base_fare != 0
            ) {
                $base_price = $category->providerservices[0]->base_fare;
            }

            if (
                $category->providerservices &&
                $category->providerservices[0]->per_miles != 0
            ) {
                $per_miles = $category->providerservices[0]->per_miles;
            }

            if (
                $category->providerservices &&
                $category->providerservices[0]->per_mins != 0
            ) {
                $per_mins = $category->providerservices[0]->per_mins;
            }

            if (
                count($category->providerservices) &&
                ($base_price != 0 || $per_miles != 0 || $per_mins != 0)
            ) {
                $category->base_price =
                    $category->providerservices[0]->base_fare;
                $category->per_mile = $category->providerservices[0]->per_miles;
                $category->per_mins = $category->providerservices[0]->per_mins;
            } else {
                $category->base_price =
                    $category->providerservices->base_fare ?? 0.0;
                $category->per_mile =
                    $category->providerservices->per_miles ?? 0.0;
                $category->per_mins =
                    $category->providerservices->per_mins ?? 0.0;
            }
        }

        return Helper::getResponse(["data" => $category]);
    }

    /**
     *
     */
    public function assign_next_provider($request_id)
    {
        try {
            $userRequest = UserRequest::where(
                "request_id",
                $request_id
            )->first();
        } catch (ModelNotFoundException $e) {
            // Cancelled between update.
            return false;
        }

        $admin_service = AdminService::find($userRequest->admin_service)
            ->where("company_id", Auth::guard("provider")->user()->company_id)
            ->first();

        if (
            $admin_service != null &&
            $admin_service->admin_service == "SERVICE"
        ) {
            $newRequest = \App\Models\Service\ServiceRequest::with(
                "user"
            )->find($userRequest->request_id);
        }

        $requestFilter = RequestFilter::where("request_id", $userRequest->id)
            ->orderBy("id")
            ->first();

        if ($requestFilter != null) {
            $requestFilter->delete();
        }

        try {
            $next_provider = RequestFilter::where(
                "request_id",
                $userRequest->id
            )
                ->orderBy("id")
                ->first();
            if ($next_provider != null) {
                $newRequest->assigned_at = Carbon::now();
                $newRequest->save();
                // incoming request push to provider
                (new SendPushNotification())->serviceIncomingRequest(
                    $next_provider->provider_id,
                    "service_incoming_request"
                );
            } else {
                $userRequest->delete();
                $newRequest->status = "CANCELLED";
                $newRequest->save();
            }
        } catch (ModelNotFoundException $e) {
            // No longer need request specific rows from RequestMeta
            $requestFilter = RequestFilter::where(
                "request_id",
                $userRequest->id
            )
                ->orderBy("id")
                ->first();
            if ($requestFilter != null) {
                $requestFilter->delete();
            }
            //  request push to user provider not available
            (new SendPushNotification())->serviceProviderNotAvailable(
                $userRequest->user_id,
                "service"
            );
        }
    }
}

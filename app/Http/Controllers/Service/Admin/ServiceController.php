<?php

namespace App\Http\Controllers\Service\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\AdminService;
use App\Models\Common\CompanyCountry;
use App\Models\Service\Service;
use App\Models\Service\ServiceCityPrice;
use App\Models\Service\ServiceRequest;
use App\Models\Service\ServiceSubCategory;
use App\Models\Service\SubService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $datum = Service::with("serviceCategory")
            ->with("subCategories")
            ->where("company_id", Auth::user()->company_id);
        if ($request->has("search_text") && $request->search_text != null) {
            $datum->Search($request->search_text);
        }
        if ($request->has("order_by")) {
            $datum->orderby($request->order_by, $request->order_direction);
        }
        $data = $datum->paginate(10);

        return Helper::getResponse(["data" => $data]);
    }

    /**
     * Store a newly created resource in storage.
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            "service_name" => 'required|max:255|regex:/^[a-zA-Z0-9\s]+$/',
            "service_category_id" => "required",
            "service_subcategory_id" => "required",
            "service_status" => "required",
        ]);
        try {
            $subCategory = new Service();
            $subCategory->company_id = Auth::user()->company_id;
            $subCategory->service_name = $request->service_name;
            $subCategory->service_category_id = $request->service_category_id;
            $subCategory->service_subcategory_id =
                $request->service_subcategory_id;
            $subCategory->service_status = $request->service_status;

            if (!empty($request->is_professional)) {
                $subCategory->is_professional = $request->is_professional;
            } else {
                $subCategory->is_professional = 0;
            }

            if (!empty($request->allow_desc)) {
                $subCategory->allow_desc = $request->allow_desc;
            } else {
                $subCategory->allow_desc = 0;
            }

            if (!empty($request->allow_before_image)) {
                $subCategory->allow_before_image = $request->allow_before_image;
            } else {
                $subCategory->allow_before_image = 0;
            }

            if (!empty($request->allow_after_image)) {
                $subCategory->allow_after_image = $request->allow_after_image;
            } else {
                $subCategory->allow_after_image = 0;
            }
            $subCategory->save();
            return Helper::getResponse([
                "status" => 200,
                "message" => trans("admin.create"),
            ]);
        } catch (\Throwable $e) {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $ServiceView = Service::with("subcategories")->findOrFail($id);

            $ServiceView["service_subcategory_data"] = ServiceSubCategory::where(
                "service_category_id",
                $ServiceView->service_category_id
            )->get();

            return Helper::getResponse(["data" => $ServiceView]);
        } catch (\Throwable $e) {
            return Helper::getResponse([
                "status" => 404,
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     * @throws ValidationException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->validate($request, [
            "service_name" => 'required|max:255|regex:/^[a-zA-Z0-9\s]+$/',
            "service_category_id" => "required",
            "service_subcategory_id" => "required",
            "service_status" => "required",
        ]);
        try {
            $serviceQuery = Service::findOrFail($id);
            if ($serviceQuery) {
                $serviceQuery->service_name = $request->service_name;
                $serviceQuery->service_category_id =
                    $request->service_category_id;
                $serviceQuery->service_subcategory_id =
                    $request->service_subcategory_id;
                $serviceQuery->service_status = $request->service_status;
                if (!empty($request->is_professional)) {
                    $serviceQuery->is_professional = $request->is_professional;
                } else {
                    $serviceQuery->is_professional = 0;
                }

                if (!empty($request->allow_desc)) {
                    $serviceQuery->allow_desc = $request->allow_desc;
                } else {
                    $serviceQuery->allow_desc = 0;
                }

                if (!empty($request->allow_before_image)) {
                    $serviceQuery->allow_before_image =
                        $request->allow_before_image;
                } else {
                    $serviceQuery->allow_before_image = 0;
                }

                if (!empty($request->allow_after_image)) {
                    $serviceQuery->allow_after_image =
                        $request->allow_after_image;
                } else {
                    $serviceQuery->allow_after_image = 0;
                }
                $serviceQuery->save();

                //Send message to socket
                $requestData = ["type" => "SERVICE_SETTING"];
                app("redis")->publish(
                    "settingsUpdate",
                    json_encode($requestData)
                );

                return Helper::getResponse([
                    "status" => 200,
                    "message" => trans("admin.update"),
                ]);
            } else {
                return Helper::getResponse([
                    "status" => 404,
                    "message" => trans("admin.not_found"),
                ]);
            }
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
    public function destroy($id): JsonResponse
    {
        // ONLY STATUS UPDATE ADDED INSTEAD OF HARD DELETE // return $this->removeModel($id);
        $subCategory = Service::findOrFail($id);
        if ($subCategory) {
            $subCategory->active_status = 2;
            $subCategory->save();
            return Helper::getResponse([
                "status" => 200,
                "message" => trans("admin.update"),
            ]);
        } else {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.not_found"),
            ]);
        }
    }

    /**
     * @param $categoryId
     * @return JsonResponse
     */
    public function subcategoriesList($categoryId): JsonResponse
    {
        $subCategories = ServiceSubCategory::select(
            "id",
            "service_subcategory_name",
            "service_subcategory_status"
        )
            ->where([
                "service_subcategory_status" => 1,
                "service_category_id" => $categoryId,
            ])
            ->get();
        return Helper::getResponse(["data" => $subCategories]);
    }

    /**
     * Display a listing of the resource.
     */
    public function getServicePriceCities($id): JsonResponse
    {
        $admin_service = AdminService::where("admin_service", "service")
            ->where("company_id", Auth::user()->company_id)
            ->value("id");
        if ($admin_service) {
            $cityList = CompanyCountry::with("country", "companyCountryCities")
                ->where("company_id", Auth::user()->company_id)
                ->where("status", 1)
                ->get();
        }
        return Helper::getResponse(["data" => $cityList]);
    }

    /**
     * Display a listing of the resource.
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $datum = Service::findOrFail($id);

            if ($request->has("status")) {
                if ($request->status == 1) {
                    $datum->service_status = 0;
                } else {
                    $datum->service_status = 1;
                }
            }
            $datum->save();

            return Helper::getResponse([
                "status" => 200,
                "message" => trans("admin.activation_status"),
            ]);
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
    public function providerServiceUpdateStatus(Request $request, $id): JsonResponse
    {
        try {
            $datum = Service::findOrFail($id);

            if ($request->has("status")) {
                if ($request->status == "APPROVED") {
                    $datum->approved_status = "PENDING";
                    $datum->service_status = 0;
                } else {
                    $datum->approved_status = "APPROVED";
                    $datum->service_status = 1;
                }
            }
            $datum->save();

            //SubService
            $subService = SubService::where("service_id", $id)->first();
            $subService->approved_status = $datum->approved_status;
            $subService->save();

            return Helper::getResponse([
                "status" => 200,
                "message" => trans("admin.activation_status"),
            ]);
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
    public function getServicePrice($service_id, $city_id)
    {
        $serviceCityPrice = ServiceCityPrice::where([
            "company_id" => Auth::user()->company_id,
            "service_id" => $service_id,
            "city_id" => $city_id,
        ])->first();
        if ($serviceCityPrice) {
            return Helper::getResponse([
                "data" => $serviceCityPrice,
                "price" => "",
            ]);
        }
        return Helper::getResponse(["data" => "", "price" => ""]);
    }

    /**
     * Display a listing of the resource.
     * @throws ValidationException
     */
    public function servicePricePost(Request $request): JsonResponse
    {
        $this->validate($request, [
            "country_id" => "required",
            "city_id" => "required",
            "base_fare" => "required|numeric",
            "per_miles" => "sometimes|nullable|numeric",
            "per_mins" => "sometimes|nullable|numeric",
            "base_distance" => "sometimes|nullable|numeric",
            "fare_type" => "required|in:FIXED,HOURLY,DISTANCETIME",
            "commission" => "required|nullable|numeric",
            "tax" => "numeric",
            "allow_quantity" => "sometimes",
            "max_quantity" => "sometimes|nullable|numeric",
        ]);
        try {
            if ($request->service_price_id != "") {
                $servicePrice = ServiceCityPrice::where(
                    "id",
                    $request->service_price_id
                )
                    ->where("city_id", $request->city_id)
                    ->first();
                if (count($servicePrice) == 0 || $servicePrice == "") {
                    $servicePrice = new ServiceCityPrice();
                }
            } else {
                $servicePrice = new ServiceCityPrice();
            }

            $servicePrice->company_id = Auth::user()->company_id;
            $servicePrice->base_fare = $request->base_fare;
            $servicePrice->country_id = $request->country_id;
            $servicePrice->city_id = $request->city_id;
            $servicePrice->service_id = $request->service_id;
            $servicePrice->fare_type = $request->fare_type;
            $servicePrice->commission = $request->commission;
            $servicePrice->tax = $request->tax;
            $servicePrice->fleet_commission = $request->fleet_commission;
            if (!empty($request->per_miles)) {
                $servicePrice->per_miles = $request->per_miles;
            } else {
                $servicePrice->per_miles = 0;
            }

            if (!empty($request->per_mins)) {
                $servicePrice->per_mins = $request->per_mins;
            } else {
                $servicePrice->per_mins = 0;
            }

            if (!empty($request->base_distance)) {
                $servicePrice->base_distance = $request->base_distance;
            } else {
                $servicePrice->base_distance = 0;
            }

            if (!empty($request->allow_quantity)) {
                $servicePrice->allow_quantity = $request->allow_quantity;
            } else {
                $servicePrice->allow_quantity = 0;
            }

            if (!empty($request->max_quantity)) {
                $servicePrice->max_quantity = $request->max_quantity;
            } else {
                $servicePrice->max_quantity = 0;
            }

            $servicePrice->save();
            return Helper::getResponse([
                "status" => 200,
                "message" => trans("admin.create"),
            ]);
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
    public function dashboarddata($id): JsonResponse
    {
        try {
            $completed = ServiceRequest::where("country_id", $id)
                ->where("status", "COMPLETED")
                ->where("company_id", Auth::user()->company_id)
                ->get(["id", "created_at", "timezone"])
                ->groupBy(function ($date) {
                    return Carbon::parse($date->created_at)->format("m");
                });
            $cancelled = ServiceRequest::where("country_id", $id)
                ->where("status", "CANCELLED")
                ->where("company_id", Auth::user()->company_id)
                ->get(["id", "created_at", "timezone"])
                ->groupBy(function ($date) {
                    return Carbon::parse($date->created_at)->format("m");
                });

            $month = [
                "01",
                "02",
                "03",
                "04",
                "05",
                "06",
                "07",
                "08",
                "09",
                "10",
                "11",
                "12",
            ];

            foreach ($month as $v) {
                if (empty($completed[$v])) {
                    $complete[] = 0;
                } else {
                    $complete[] = count($completed[$v]);
                }

                if (empty($cancelled[$v])) {
                    $cancel[] = 0;
                } else {
                    $cancel[] = count($cancelled[$v]);
                }
            }

            $overall = ServiceRequest::where("country_id", $id)
                ->where("status", "COMPLETED")
                ->where("company_id", Auth::user()->company_id)
                ->count();

            $data["cancelled_data"] = $cancel;
            $data["completed_data"] = $complete;
            $data["max"] = max($complete);
            $data["overall"] = $overall;
            if (max($complete) < max($cancel)) {
                $data["max"] = max($cancel);
            }

            return Helper::getResponse(["status" => 200, "data" => $data]);
        } catch (\Exception $e) {
            return Helper::getResponse([
                "status" => 500,
                "message" => trans("api.something_went_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }
}

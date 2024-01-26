<?php

namespace App\Http\Controllers\Service\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\Dispute;
use App\Models\Common\Provider;
use App\Models\Common\UserRequest;
use App\Models\Service\MainService;
use App\Models\Service\ProjectCategory;
use App\Models\Service\Service;
use App\Models\Service\ServiceCategory;
use App\Models\Service\ServiceCityPrice;
use App\Models\Service\ServiceRequest;
use App\Models\Service\ServiceRequestDispute;
use App\Models\Service\ServiceSubcategory;
use App\Services\V1\Common\UserServices;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class HomeController extends Controller
{
    public function service_category(Request $request): JsonResponse
    {
        $service_list = ServiceCategory::where(
            "company_id",
            Auth::guard("user")->user()->company_id
        )->get();
        return Helper::getResponse(["data" => $service_list]);
    }

    //Service Sub Category
    public function service_sub_category(Request $request, $id): JsonResponse
    {
        $service_sub_category_list = ServiceSubcategory::where(
            "company_id",
            Auth::guard("user")->user()->company_id
        )
            ->where("service_subcategory_status", 1)
            ->where("service_category_id", $id)
            ->get();
        return Helper::getResponse(["data" => $service_sub_category_list]);
    }

    //Service Sub Project Category
    public function projectcategories(Request $request, $id): JsonResponse
    {
        Log::info(implode(", ", $request->all()));
        $service_project_category_list = ProjectCategory::where(
            "company_id",
            Auth::guard("user")->user()->company_id
        )
            ->where("service_projectcategory_status", 1)
            ->where("service_project_category_id", $id)
            ->get();
        return Helper::getResponse(["data" => $service_project_category_list]);
    }

    //Service Sub Project Category
    public function main_services(Request $request, $id)
    {
        Log::info($request->all());
        $service_main_service_list = MainService::where(
            "company_id",
            Auth::guard("user")->user()->company_id
        )
            ->where("service_main_service_status", 1)
            ->where("service_main_service_id", $id)
            ->get();
        return Helper::getResponse(["data" => $service_main_service_list]);
    }

    //Service Sub Category
    public function service($category_id, $subcategory_id): JsonResponse
    {
        $service = Service::with([
            "service_city" => function ($query) {
                $query->where("city_id", Auth::guard("user")->user()->city_id);
            },
        ])
            ->where("company_id", Auth::guard("user")->user()->company_id)
            ->where("service_subcategory_id", $subcategory_id)
            ->where("service_category_id", $category_id)
            ->where("service_status", 1)
            ->get();
        return Helper::getResponse(["data" => $service]);
    }

    public function service_city_price(Request $request, $id): JsonResponse
    {
        $service_city_price = ServiceCityPrice::with("service")
            ->where("company_id", Auth::guard("user")->user()->company_id)
            ->where("fare_type", "FIXED")
            ->where("city_id", Auth::guard("user")->user()->city_id)
            ->where("service_id", $id)
            ->get();
        return Helper::getResponse(["data" => $service_city_price]);
    }

    public function trips(Request $request): JsonResponse
    {
        try {
            $jsonResponse = [];
            $jsonResponse["type"] = "service";
            $withCallback = [
                "payment",
                "service" => function ($query) {
                    $query->select("id", "service_name");
                },
                "user" => function ($query) {
                    $query->select(
                        "id",
                        "first_name",
                        "last_name",
                        "rating",
                        "picture",
                        "currency_symbol"
                    );
                },
                "provider" => function ($query) {
                    $query->select(
                        "id",
                        "first_name",
                        "last_name",
                        "rating",
                        "picture",
                        "mobile"
                    );
                },
                "rating",
            ];
            $userRequest = ServiceRequest::select(
                "id",
                "booking_id",
                "user_id",
                "provider_id",
                "service_id",
                "status",
                "s_address",
                "assigned_at",
                "created_at",
                "timezone",
                "user_rated",
                "provider_rated"
            );
            $data = (new UserServices())->userHistory(
                $request,
                $userRequest,
                $withCallback
            );
            $jsonResponse["total_records"] = count($data);
            $jsonResponse["service"] = $data;
            return Helper::getResponse(["data" => $jsonResponse]);
        } catch (Exception $e) {
            return response()->json([
                "error" => trans("api.something_went_wrong"),
            ]);
        }
    }

    public function gettripdetails(Request $request, $id): JsonResponse
    {
        try {
            $jsonResponse = [];
            $jsonResponse["type"] = "service";
            $request->request->add(["admin_service" => "SERVICE", "id" => $id]);
            $userRequest = ServiceRequest::with([
                "provider",
                "payment",
                "service.servicesubCategory",
                "dispute" => function ($query) {
                    $query->where("dispute_type", "user");
                },
            ]);

            $data = (new UserServices())->userTripsDetails(
                $request,
                $userRequest
            );
            $jsonResponse["service"] = $data;
            return Helper::getResponse(["data" => $jsonResponse]);
        } catch (Exception $e) {
            return response()->json([
                "error" => trans("api.something_went_wrong"),
            ]);
        }
    }

    /**
     * @throws ValidationException
     */
    public function service_request_dispute(Request $request): JsonResponse
    {
        $this->validate($request, [
            "dispute_name" => "required",
            "dispute_type" => "required",
            "provider_id" => "required",
            "user_id" => "required",
            "id" => "required",
        ]);
        $service_request_dispute = ServiceRequestDispute::where(
            "company_id",
            Auth::guard("user")->user()->company_id
        )
            ->where("service_request_id", $request->id)
            ->where("dispute_type", "user")
            ->first();
        $request->request->add(["admin_service" => "SERVICE"]);

        if ($service_request_dispute == null) {
            try {
                $disputeRequest = new ServiceRequestDispute();
                (new UserServices())->userDisputeCreate(
                    $request,
                    $disputeRequest
                );
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
        } else {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans(
                    "Already Dispute Created for the Ride Request"
                ),
            ]);
        }
    }

    public function get_service_request_dispute(
        Request $request,
                $id
    ): JsonResponse
    {
        $service_request_dispute = ServiceRequestDispute::where(
            "company_id",
            Auth::guard("user")->user()->company_id
        )
            ->where("service_request_id", $id)
            ->where("dispute_type", "user")
            ->first();
        return Helper::getResponse(["data" => $service_request_dispute]);
    }

    public function getdisputedetails(Request $request): JsonResponse
    {
        $dispute = Dispute::select("id", "dispute_name", "service")
            ->where("service", "SERVICE")
            ->where("dispute_type", "user")
            ->where("status", "active")
            ->get();
        return Helper::getResponse(["data" => $dispute]);
    }

    public function getUserdisputedetails(Request $request): JsonResponse
    {
        $dispute = Dispute::select("id", "dispute_name", "service")
            ->where("service", "SERVICE")
            ->where("dispute_type", "provider")
            ->where("status", "active")
            ->get();
        return Helper::getResponse(["data" => $dispute]);
    }

    public function zipcodeprovider(Request $request): JsonResponse
    {
        $provider = Provider::where("zipcode", $request->zipcode)->get();
        return Helper::getResponse(["data" => $provider]);
    }

    public function request_details(Request $request): JsonResponse
    {
        $userRequest = UserRequest::where("status", "INITIATED")->get();

        return Helper::getResponse(["data" => $userRequest]);
    }
}

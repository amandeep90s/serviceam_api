<?php

namespace App\Http\Controllers\Common\Admin\Resource;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\City;
use App\Models\Common\CompanyCity;
use App\Models\Common\CompanyCityAdminService;
use App\Models\Common\State;
use App\Traits\Actions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CompanyCityController extends Controller
{
    use Actions;

    private CompanyCity $model;
    private $request;

    public function __construct(CompanyCity $model)
    {
        $this->model = $model;
    }

    public function index(Request $request): JsonResponse
    {
        $datum = CompanyCity::with(
            "country",
            "city",
            "state",
            "city_service"
        )->where("company_id", Auth::user()->company_id);

        if ($request->has("search_text") && $request->search_text != null) {
            $datum->Search($request->search_text);
        }

        if ($request->has("order_by")) {
            $datum->orderby($request->order_by, $request->order_direction);
        }

        $datum = $datum->paginate(10);
        return Helper::getResponse(["data" => $datum]);
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate(
            $request,
            [
                "country_id" => "required",
                "state_id" => "required",
                "city_id" => "required",
                "status" => "required",
                "other_city" => "required_if:city_id,other",
            ],
            ["other_city.required_if" => "City Name is required"]
        );

        try {
            if ($request->has("other_city") && $request->city_id == "other") {
                $City = new City();
                $City->country_id = $request->country_id;
                $City->state_id = $request->state_id;
                $City->city_name = $request->other_city;
                $City->status = $request->status;
                $City->save();

                $city_id = $City->id;
            } else {
                $city_id = $request->city_id;
            }

            $company_city_service = new CompanyCity();
            $company_city_service->company_id = Auth::user()->company_id;
            $company_city_service->country_id = $request->country_id;
            $company_city_service->state_id = $request->state_id;
            $company_city_service->city_id = $city_id;
            $company_city_service->status = $request->status;
            $company_city_service->save();

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

    public function show($id): JsonResponse
    {
        try {
            $company_city_service = CompanyCity::with(
                "city_service"
            )->findOrFail($id);
            $company_city_service["admin_service"] = CompanyCityAdminService::where("company_city_service_id", $id)
                ->pluck("admin_service")
                ->all();
            $company_cities = CompanyCity::where(
                "company_id",
                Auth::user()->company_id
            )
                ->where("id", "!=", $id)
                ->pluck("city_id")
                ->all();

            $company_city_service["state_data"] = State::where(
                "country_id",
                $company_city_service["country_id"]
            )->get();
            $company_city_service["city_data"] = City::where(
                "state_id",
                $company_city_service["state_id"]
            )
                ->whereNotIn("id", $company_cities)
                ->get();

            return Helper::getResponse(["data" => $company_city_service]);
        } catch (\Throwable $e) {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->validate($request, [
            "country_id" => "required",
            "state_id" => "required",
            "city_id" => "required",
            /*'admin_service' => 'required|in:TRANSPORT,ORDER,SERVICE',*/
            "status" => "required",
        ]);

        try {
            $company_city_service = CompanyCity::findOrFail($id);
            $company_city_service->country_id = $request->country_id;
            $company_city_service->state_id = $request->state_id;
            $company_city_service->city_id = $request->city_id;
            $company_city_service->status = $request->status;
            $company_city_service->update();

            return Helper::getResponse([
                "status" => 200,
                "message" => trans("admin.update"),
            ]);
        } catch (\Throwable $e) {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    public function countrycities(Request $request, $country_id): JsonResponse
    {
        $country_city = CompanyCity::where("country_id", $country_id)
            ->with("city")
            ->get();
        return Helper::getResponse(["data" => $country_city]);
    }

    public function destroy($id): JsonResponse
    {
        return $this->removeModel($id);
    }
}

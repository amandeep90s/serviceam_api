<?php

namespace App\Http\Controllers\Common\Admin\Resource;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\CompanyCity;
use App\Models\Common\CompanyCountry;
use App\Models\Common\Zone;
use App\Traits\Actions;
use App\Traits\Encryptable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ZoneController extends Controller
{
    use Actions;
    use Encryptable;

    private Zone $model;
    private $request;

    public function __construct(Zone $model)
    {
        $this->model = $model;
    }

    public function index(Request $request): JsonResponse
    {
        $datum = Zone::with("city")->where(
            "company_id",
            Auth::user()->company_id
        );

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
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            "name" => "required|max:255",
            "country_id" => "required",
            "city_id" => "required",
        ]);

        try {
            $request->request->add(["company_id" => Auth::user()->company_id]);
            $zone = $request->all();
            $returndata = Zone::create($zone);
            return Helper::getResponse([
                "status" => 200,
                "message" => trans("admin.create"),
                "data" => $returndata,
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
            $zone = Zone::findOrFail($id);
            $zone["city_data"] = CompanyCity::where("city_id", $zone["city_id"])
                ->with("city")
                ->get();
            $zone["country_data"] = CompanyCountry::where(
                "company_id",
                $zone["company_id"]
            )
                ->with("country")
                ->get();
            $zone["country_id"] = $zone["city_data"][0]["country_id"];
            return Helper::getResponse(["data" => $zone]);
        } catch (\Throwable $e) {
            return Helper::getResponse([
                "status" => 404,
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
            "name" => "required|max:255",
            "city_id" => "required",
        ]);
        try {
            $zone = Zone::findOrFail($id);
            $zone->name = $request->name;
            $zone->city_id = $request->city_id;
            $zone->user_type = $request->user_type;
            $zone->save();
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

    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $datum = Zone::findOrFail($id);
            if ($request->status == "ACTIVE") {
                $datum->status = "INACTIVE";
            } else {
                $datum->status = "ACTIVE";
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

    public function destroy($id): JsonResponse
    {
        Zone::findOrFail($id);
        return $this->removeModel($id);
    }

    public function cityzones(): JsonResponse
    {
        $city_zone = Zone::with("city")
            ->where("company_id", Auth::user()->company_id)
            ->get();
        return Helper::getResponse(["data" => $city_zone]);
    }

    public function cityzonestype(Request $request, $id): JsonResponse
    {
        if (!empty(Auth::user())) {
            $this->company_id = Auth::user()->company_id;
        } else {
            $this->company_id = Auth::guard("shop")->user()->company_id;
        }

        $city_zone = Zone::where("city_id", $id)
            ->where("company_id", $this->company_id)
            ->where("user_type", $request->type)
            ->get();
        return Helper::getResponse(["data" => $city_zone]);
    }
}

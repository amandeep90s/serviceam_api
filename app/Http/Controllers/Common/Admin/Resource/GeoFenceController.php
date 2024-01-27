<?php

namespace App\Http\Controllers\Common\Admin\Resource;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\CompanyCity;
use App\Models\Common\GeoFence;
use App\Traits\Actions;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class GeoFenceController extends Controller
{
    use Actions;

    private GeoFence $model;
    private $request;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(GeoFence $model)
    {
        $this->model = $model;
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index(Request $request): JsonResponse
    {
        $datum = GeoFence::with("city")->where(
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
     * Store a newly created resource in storage.
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            "city_id" => "required|numeric",
            "location_name" => "required",
            "ranges" => "required",
        ]);

        try {
            $geofence = new GeoFence();
            $geofence->company_id = Auth::user()->company_id;
            $geofence->city_id = $request->city_id;
            $geofence->location_name = $request->location_name;
            $geofence->ranges = $request->ranges;
            $geofence->save();
            return Helper::getResponse([
                "status" => 200,
                "message" => trans("admin.create"),
            ]);
        } catch (ModelNotFoundException $e) {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     */
    public function show($id): JsonResponse
    {
        try {
            $geofence = GeoFence::findOrFail($id);
            $country = CompanyCity::where(
                "city_id",
                $geofence->city_id
            )->first();
            $geofence->country_id = $country->country_id;
            return Helper::getResponse(["data" => $geofence]);
        } catch (\Throwable $e) {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
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
            "city_id" => "required|numeric",
            "location_name" => "required",
            "ranges" => "required",
        ]);

        try {
            $geofence = GeoFence::findOrFail($id);
            $geofence->company_id = Auth::user()->company_id;
            $geofence->city_id = $request->city_id;
            $geofence->location_name = $request->location_name;
            $geofence->ranges = $request->ranges;
            $geofence->save();

            return Helper::getResponse([
                "status" => 200,
                "message" => trans("admin.update"),
            ]);
        } catch (ModelNotFoundException $e) {
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
            $datum = GeoFence::findOrFail($id);

            if ($request->has("status")) {
                if ($request->status == 1) {
                    $datum->status = 0;
                } else {
                    $datum->status = 1;
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
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        return $this->removeModel($id);
    }
}

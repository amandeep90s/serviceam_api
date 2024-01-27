<?php

namespace App\Http\Controllers\Common\Admin\Resource;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\PayrollTemplate;
use App\Traits\Actions;
use App\Traits\Encryptable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PayrollTemplateController extends Controller
{
    use Actions, Encryptable;

    private PayrollTemplate $model;
    private $request;

    public function __construct(Payrolltemplate $model)
    {
        $this->model = $model;
    }

    public function index(Request $request): JsonResponse
    {
        $datum = PayrollTemplate::with("zone")->where(
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
            "template_name" => "required|max:255",
            "zone_id" => "required",
        ]);

        try {
            $request->request->add(["company_id" => Auth::user()->company_id]);
            $zone = $request->all();
            $returndata = PayrollTemplate::create($zone);
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
            $zone = PayrollTemplate::findOrFail($id);

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
            "template_name" => "required|max:255",
            "zone_id" => "required",
        ]);
        try {
            $zone = PayrollTemplate::findOrFail($id);
            $zone->template_name = $request->template_name;
            $zone->zone_id = $request->zone_id;
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
            $datum = PayrollTemplate::findOrFail($id);
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
        $datum = PayrollTemplate::findOrFail($id);
        return $this->removeModel($id);
    }

    public function zonetemplates(): JsonResponse
    {
        $zone_template = PayrollTemplate::where(
            "company_id",
            Auth::user()->company_id
        )->get();
        return Helper::getResponse(["data" => $zone_template]);
    }
}

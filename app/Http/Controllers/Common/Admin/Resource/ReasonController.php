<?php

namespace App\Http\Controllers\Common\Admin\Resource;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\Reason;
use App\Traits\Actions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ReasonController extends Controller
{
    use Actions;

    private Reason $model;
    private $request;

    public function __construct(Reason $model)
    {
        $this->model = $model;
    }

    public function index(Request $request): JsonResponse
    {
        $datum = Reason::where("company_id", Auth::user()->company_id);

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
            "type" => "required",
            "reason" => "required",
            "status" => "required",
            "service" => "required",
        ]);

        try {
            $request->request->add(["company_id" => \Auth::user()->company_id]);
            $reason = new reason();
            $reason->company_id = Auth::user()->company_id;
            $reason->service = $request->service;
            $reason->type = $request->type;
            $reason->reason = $request->reason;
            $reason->status = $request->status;
            $reason->save();
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
            $reason = Reason::findOrFail($id);
            return Helper::getResponse(["data" => $reason]);
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
            "type" => "required",
            "reason" => "required",
            "service" => "required",
        ]);

        try {
            $reason = Reason::findOrFail($id);
            $reason->service = $request->service;
            $reason->type = $request->type;
            $reason->reason = $request->reason;
            $reason->status = $request->status;
            $reason->save();
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

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        return $this->removeModel($id);
    }
}

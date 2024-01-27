<?php

namespace App\Http\Controllers\Common\Admin\Resource;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\Document;
use App\Traits\Actions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DocumentController extends Controller
{
    use Actions;

    private Document $model;
    private $request;

    public function __construct(Document $model)
    {
        $this->model = $model;
    }

    public function index(Request $request): JsonResponse
    {
        $datum = Document::where("company_id", Auth::user()->company_id);

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
            "type" => "required|in:TRANSPORT,ORDER,SERVICE,ALL",
        ]);

        try {
            $document = new Document();
            $document->name = $request->name;
            $document->company_id = Auth::user()->company_id;
            $document->type = $request->type;
            $document->file_type = $request->file_type;
            $document->is_backside = $request->is_backside;
            $document->save();
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
            $document = Document::findOrFail($id);
            return Helper::getResponse(["data" => $document]);
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
            "name" => "required|max:255",
            "type" => "required|in:TRANSPORT,ORDER,SERVICE,ALL",
        ]);

        try {
            Document::where("id", $id)->update([
                "name" => $request->name,
                "type" => $request->type,
            ]);

            $document = Document::where("id", $id)->first();
            $document->name = $request->name;
            $document->type = $request->type;
            $document->file_type = $request->file_type;
            if ($request->has("is_backside")) {
                $document->is_backside = $request->is_backside;
            } else {
                $document->is_backside = 0;
            }

            $document->save();

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

    public function destroy($id): JsonResponse
    {
        return $this->removeModel($id);
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $datum = Document::findOrFail($id);

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
}

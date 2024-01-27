<?php

namespace App\Http\Controllers\Service\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\Dispute;
use App\Models\Service\Service;
use App\Models\Service\ServiceRequestDispute;
use App\Services\Transactions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ServiceRequestDisputeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $datum = ServiceRequestDispute::where(
            "company_id",
            Auth::user()->company_id
        )
            ->with("user", "provider", "request")
            ->orderBy("created_at", "desc");

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
            "request_id" => "required",
            "dispute_type" => "required",
            "dispute_name" => "required",
        ]);

        try {
            $dispute = new ServiceRequestDispute();
            $dispute->company_id = Auth::user()->company_id;
            $dispute->service_request_id = $request->request_id;
            $dispute->dispute_type = $request->dispute_type;
            $dispute->user_id = $request->user_id;
            $dispute->provider_id = $request->provider_id;
            $dispute->dispute_name = $request->dispute_name;
            if (!empty($request->dispute_other)) {
                $dispute->dispute_name = $request->dispute_other;
            }
            $dispute->comments = $request->comments;
            $dispute->save();

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
            $requestDispute = ServiceRequestDispute::with(
                "user",
                "provider",
                "request"
            )->findOrFail($id);
            $serviceQuery = Service::where(
                "id",
                $requestDispute->request->service_id
            )->first();
            $requestDispute->service = $serviceQuery;
            return Helper::getResponse(["data" => $requestDispute]);
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
            "comments" => "required",
            "status" => "required",
        ]);

        try {
            $dispute = ServiceRequestDispute::findOrFail($id);
            $dispute->comments = $request->comments;
            $dispute->refund_amount = $request->refund_amount;
            $dispute->status = "closed";
            $dispute->save();
            if ($request->refund_amount > 0) {
                $transaction["message"] = "Service amount refund";
                $transaction["amount"] = $request->refund_amount;
                $transaction["company_id"] = $dispute->company_id;
                if ($dispute->dispute_type == "user") {
                    $transaction["id"] = $dispute->user_id;
                    (new Transactions())->disputeCreditDebit($transaction);
                } else {
                    $transaction["id"] = $dispute->provider_id;
                    (new Transactions())->disputeCreditDebit($transaction, 0);
                }
            }

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
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        // ONLY STATUS UPDATE ADDED INSTEAD OF HARD DELETE // return $this->removeModel($id);
        $service = ServiceRequestDispute::findOrFail($id);
        if ($service) {
            $service->active_status = 2;
            $service->save();
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
     * Remove the specified resource from storage.
     * @throws ValidationException
     */
    public function dispute_list(Request $request)
    {
        $this->validate($request, [
            "dispute_type" => "required",
        ]);

        return Dispute::select("dispute_name")
            ->where("dispute_type", $request->dispute_type)
            ->where("status", "active")
            ->get();
    }
}

<?php

namespace App\Http\Controllers\Common\Admin\Resource;

use App\Exports\PayrollsExport;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\Payroll;
use App\Models\Common\PayrollTemplate;
use App\Models\Common\Provider;
use App\Models\Common\Zone;
use App\Traits\Actions;
use App\Traits\Encryptable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class PayrollController extends Controller
{
    use Actions, Encryptable;

    private Payroll $model;
    private $request;

    public function __construct(Payroll $model)
    {
        $this->model = $model;
    }

    public function index(Request $request): JsonResponse
    {
        $datum = Payroll::where("company_id", Auth::user()->company_id);

        if ($request->has("search_text") && $request->search_text != null) {
            $datum->Search($request->search_text);
        }

        if ($request->has("order_by")) {
            $datum->orderby($request->order_by, $request->order_direction);
        }
        $datum->groupBy("transaction_id");

        $data = $datum->paginate(10);

        return Helper::getResponse(["data" => $data]);
    }

    public function show($id): JsonResponse
    {
        try {
            $payroll_item_list = Payroll::with("provider")
                ->where("transaction_id", $id)
                ->get();
            return Helper::getResponse(["data" => $payroll_item_list]);
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
            "provider.*" => "required",
            "wallet.*" => "required",
        ]);
        try {
            $type = Zone::find($request->zone_id)->user_type;

            $company_id = Auth::user()->company_id;
            $providers = $request->proid;
            $all_records = $request->pid;
            $wallets = $request->wallet;
            $zones = $request->zones;
            Payroll::where("transaction_id", $id)->delete();
            foreach ($all_records as $key => $val) {
                if ($request->type == "manual") {
                    $transac = [
                        "transaction_id" => $id,
                        "company_id" => $company_id,
                        "template_id" => $request->template_id,
                        "provider_id" => $providers[$val],
                        "wallet" => $wallets[$val],
                        "payroll_type" => "MANUAL",
                        "type" => $type,
                        "admin_service" => "",
                    ];
                } else {
                    $transac = [
                        "transaction_id" => $id,
                        "company_id" => $company_id,
                        "zone_id" => $zones[$val],
                        "provider_id" => $providers[$val],
                        "wallet" => $wallets[$val],
                        "payroll_type" => "TEMPLATE",
                        "type" => $type,
                        "admin_service" => "",
                    ];
                }
                Payroll::create($transac);
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

    public function updatePayroll(Request $request): JsonResponse
    {
        try {
            $payroll = Payroll::findOrFail($request->id);
            $payroll->status = $request->status;
            $payroll->save();
            return Helper::getResponse([
                "status" => 200,
                "message" => trans("admin.activation_status"),
            ]);
        } catch (\Throwable $e) {
            return Helper::getResponse([
                "status" => 500,
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
        try {
            $datum = Payroll::where("transaction_id", $id)->delete();

            return Helper::getResponse([
                "message" => trans("admin.user_msgs.user_delete"),
            ]);
        } catch (\Throwable $e) {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.user_msgs.user_not_found"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    public function zoneprovider(Request $request, $id): JsonResponse
    {
        $zone_id = 0;
        $zone = PayrollTemplate::find($id);
        if ($zone->count() > 0) {
            $zone_id = $zone->id;
        }
        $data = Provider::where("zone_id", $zone_id)
            ->where("wallet_balance", ">", 1)
            ->select(
                "id",
                "status",
                "zone_id",
                "first_name",
                "last_name",
                "wallet_balance"
            );
        $providers = $data->get();
        return Helper::getResponse(["data" => $providers]);
    }

    public function PayrollDownload(Request $request, $id)
    {
        $data = Payroll::with([
            "provider",
            "bankDetails"
        ])
            ->where("transaction_id", $id)
            ->select("id", "transaction_id", "provider_id", "wallet")
            ->get();
        $data->map(function ($payroll) {
            $payroll->provider_id =
                $payroll->provider->first_name .
                " " .
                $payroll->provider->last_name;
            $bankDetails = "";
            foreach ($payroll->bankDetails as $val) {
                $bankDetails .= $val->keyvalue . "--";
            }
            $payroll->bank_details = $bankDetails;
            unset($payroll->provider);
            unset($payroll->bankDetails);
            return $payroll;
        });
        $filename = "payroll" . ".xlsx";
        Excel::store(new PayrollsExport($data), $filename, "public");
        return redirect("storage/{$filename}");
    }

    public function store(Request $request): JsonResponse
    {
        $messages = [
            "pid.*" => "checked atleast anyone checkbox",
            "wallet.*" => "wallet field is required ",
        ];
        $this->validate(
            $request,
            ["pid.*" => "required", "wallet.*" => "required"],
            $messages
        );

        try {
            if ($request->has("zone_id")) {
                $zone_id = $request->zone_id;
            } else {
                $zone_id = PayrollTemplate::find($request->template_id)
                    ->zone_id;
            }

            $type = Zone::find($zone_id)->user_type;

            $transaction_id = "GOPAY" . time();
            $company_id = Auth::user()->company_id;
            $providers = $request->pid;
            $wallets = $request->wallet;
            $zones = $request->zones;
            foreach ($providers as $key => $val) {
                if ($request->type == "manual") {
                    $transac = [
                        "transaction_id" => $transaction_id,
                        "company_id" => $company_id,
                        "zone_id" => $request->zone_id,
                        "provider_id" => $val,
                        "wallet" => $wallets[$val],
                        "payroll_type" => "MANUAL",
                        "type" => $type,
                        "admin_service" => "",
                    ];
                } else {
                    $transac = [
                        "transaction_id" => $transaction_id,
                        "company_id" => $company_id,
                        "template_id" => $request->template_id,
                        "provider_id" => $val,
                        "wallet" => $wallets[$val],
                        "payroll_type" => "TEMPLATE",
                        "type" => $type,
                        "admin_service" => "",
                    ];
                }
                Payroll::create($transac);
            }
            $returndata = Payroll::where(
                "company_id",
                Auth::user()->company_id
            )->paginate(10);
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
}

<?php

namespace App\Http\Controllers\Common\Admin\Resource;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\Dispute;
use App\Traits\Actions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DisputeController extends Controller
{
    use Actions;

    private Dispute $model;
    private $request;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Dispute $model)
    {
        $this->model = $model;
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index(Request $request)
    {
        $datum = Dispute::where("company_id", Auth::user()->company_id);

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
     * Store a newly created resource in storage.
     *
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            "service" => "required",
            "dispute_type" => "required",
            "dispute_name" => "required",
            "status" => "required",
        ]);

        try {
            $request->request->add(["company_id" => Auth::user()->company_id]);
            $Dispute = new Dispute();
            $Dispute->company_id = Auth::user()->company_id;
            $Dispute->service = $request->service;
            $Dispute->admin_services = $request->service;
            $Dispute->dispute_type = $request->dispute_type;
            $Dispute->dispute_name = $request->dispute_name;
            $Dispute->status = $request->status;
            $Dispute->save();

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
     *
     */
    public function show($id)
    {
        try {
            $dispute = Dispute::findOrFail($id);
            return Helper::getResponse(["data" => $dispute]);
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
     *
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            "service" => "required",
            "dispute_type" => "required",
            "dispute_name" => "required",
            "status" => "required",
        ]);

        try {
            $Dispute = Dispute::findOrFail($id);
            $Dispute->service = $request->service;
            $Dispute->admin_services = $request->service;
            $Dispute->dispute_type = $request->dispute_type;
            $Dispute->dispute_name = $request->dispute_name;
            $Dispute->status = $request->status;
            $Dispute->save();

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
     *
     */
    public function destroy($id)
    {
        return $this->removeModel($id);
    }
}

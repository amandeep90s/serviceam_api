<?php

namespace App\Http\Controllers\Common\Admin\Resource;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\Notification;
use App\Models\Common\NotificationDay;
use App\Traits\Actions;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class NotificationController extends Controller
{
    use Actions;

    private Notification $model;
    private $request;

    public function __construct(Notification $model)
    {
        $this->model = $model;
    }

    public function index(Request $request): JsonResponse
    {
        $datum = Notification::where("company_id", Auth::user()->company_id);

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
            "notify_type" => "required",
            "service" => "required",
            "image" => "required|mimes:jpeg,jpg,png|max:5242880",
        ]);
        try {
            $Notifications = new Notification();
            $Notifications->notify_type = $request->notify_type;
            if ($request->hasFile("image")) {
                $Notifications->image = Helper::uploadFile(
                    $request->file("image"),
                    "Notification/image"
                );
            }
            $Notifications->company_id = Auth::user()->company_id;
            $Notifications->service = $request->service;
            $Notifications->descriptions = $request->descriptions;
            $Notifications->title = $request->title;
            $Notifications->expiry_date = date(
                "Y-m-d H:i:s",
                strtotime($request->expiry_date)
            );
            $Notifications->status = $request->status;
            $Notifications->save();
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
            $notification = Notification::findOrFail($id);

            return Helper::getResponse(["data" => $notification]);
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
            "notify_type" => "required",
            "service" => "required",
        ]);
        try {
            $notifications = Notification::findOrFail($id);
            $notifications->notify_type = $request->notify_type;
            if ($request->hasFile("image")) {
                $notifications->image = Helper::uploadFile(
                    $request->file("image"),
                    "Notification/image"
                );
            }
            $notifications->service = $request->service;
            $notifications->descriptions = $request->descriptions;
            $notifications->title = $request->title;
            $notifications->expiry_date = date(
                "Y-m-d H:i:s",
                strtotime($request->expiry_date)
            );
            $notifications->status = $request->status;
            $notifications->save();
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

    public function daysindex(Request $request): JsonResponse
    {
        $datum = NotificationDay::where(
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
    public function daysstore(Request $request): JsonResponse
    {
        $this->validate($request, ["days" => "required"]);
        try {
            $Notifications = new NotificationDay();
            $Notifications->company_id = Auth::user()->company_id;
            $Notifications->days = $request->days;
            $Notifications->status = $request->status;
            $Notifications->save();
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

    public function daysshow($id): JsonResponse
    {
        try {
            $notification = NotificationDay::findOrFail($id);
            return Helper::getResponse(["data" => $notification]);
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
    public function daysupdate(Request $request, $id): JsonResponse
    {
        $this->validate($request, ["days" => "required"]);
        try {
            $Notifications = NotificationDay::findOrFail($id);
            $Notifications->days = $request->days;
            $Notifications->status = $request->status;
            $Notifications->save();
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
}

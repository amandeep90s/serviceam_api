<?php

namespace App\Http\Controllers\Service\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\Menu;
use App\Models\Service\ServiceCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $datum = ServiceCategory::where("company_id", Auth::user()->company_id);
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
            "service_category_name" =>
                'required|max:255|regex:/^[a-zA-Z0-9\s]+$/',
            "service_category_alias_name" =>
                'required|max:255|regex:/^[a-zA-Z0-9\s]+$/',
            "picture" => "mimes:jpeg,jpg,bmp,png|max:5242880",
            "service_category_status" => "required",
        ]);
        try {
            $serviceCategory = new ServiceCategory();
            $serviceCategory->company_id = Auth::user()->company_id;
            $serviceCategory->service_category_name =
                $request->service_category_name;
            $serviceCategory->alias_name =
                $request->service_category_alias_name;
            $serviceCategory->favorites = $request->favorites;
            $serviceCategory->service_category_status =
                $request->service_category_status;
            $serviceCategory->price_choose = $request->price_choose;
            if ($request->hasFile("picture")) {
                $serviceCategory->picture = Helper::upload_file(
                    $request->file("picture"),
                    "services",
                    "cat-" . time() . ".png"
                );
            }
            $serviceCategory->save();
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
            $serviceCategory = ServiceCategory::findOrFail($id);
            return Helper::getResponse(["data" => $serviceCategory]);
        } catch (\Throwable $e) {
            return Helper::getResponse([
                "status" => 404,
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceCategory $serviceCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @throws ValidationException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->validate($request, [
            "service_category_name" =>
                'required|max:255|regex:/^[a-zA-Z0-9\s]+$/',
            "service_category_alias_name" =>
                'required|max:255|regex:/^[a-zA-Z0-9\s]+$/',
            "picture" => "mimes:jpeg,jpg,bmp,png|max:5242880",
            //'service_category_order' => 'required|integer|between:0,10',
            "service_category_status" => "required",
        ]);
        try {
            $serviceCategory = ServiceCategory::findOrFail($id);
            if ($serviceCategory) {
                $serviceCategory->service_category_name =
                    $request->service_category_name;
                $serviceCategory->alias_name =
                    $request->service_category_alias_name;
                $serviceCategory->service_category_status =
                    $request->service_category_status;
                $serviceCategory->price_choose = $request->price_choose;

                $serviceCategory->favorites = $request->favorites;
                if ($request->hasFile("picture")) {
                    $serviceCategory->picture = Helper::upload_file(
                        $request->file("picture"),
                        "services",
                        "cat-" . time() . ".png"
                    );
                }
                $serviceCategory->save();
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
        $serviceCategory = ServiceCategory::findOrFail($id);
        if ($serviceCategory) {
            $serviceCategory->service_category_status = 2;
            $serviceCategory->save();
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
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $datum = ServiceCategory::findOrFail($id);

            if ($request->has("status") && $request->status == 1) {
                $datum->service_category_status = 0;
            } else {
                $datum->service_category_status = 1;
            }
            $datum->save();

            $menu = Menu::where("menu_type_id", $id)
                ->where("admin_service", "SERVICE")
                ->where("company_id", Auth::user()->company_id)
                ->first();
            if (!empty($menu)) {
                $menu->status = $datum->service_category_status;
                $menu->save();
            }

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

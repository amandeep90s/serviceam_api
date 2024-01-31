<?php

namespace App\Http\Controllers\Service\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Service\ServiceCategory;
use App\Models\Service\ServiceSubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ServiceSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $datum = ServiceSubCategory::with("serviceCategory")->where(
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
            "service_subcategory_name" =>
            'required|max:255|regex:/^[a-zA-Z0-9\s]+$/',
            "service_category_id" => "required",
            /*'picture' => 'mimes:jpeg,jpg,bmp,png|max:5242880',
             'service_subcategory_order' => 'required|integer|between:0,10',*/
            "service_subcategory_status" => "required",
        ]);
        try {
            $subCategory = new ServiceSubCategory();
            $subCategory->company_id = Auth::user()->company_id;
            $subCategory->service_subcategory_name =
                $request->service_subcategory_name;
            $subCategory->service_category_id = $request->service_category_id;
            $subCategory->service_subcategory_status =
                $request->service_subcategory_status;
            $subCategory->save();
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
    public function show($id)
    {
        try {
            $serviceSubcategory = ServiceSubCategory::findOrFail($id);
            return Helper::getResponse(["data" => $serviceSubcategory]);
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
            "service_subcategory_name" =>
            'required|max:255|regex:/^[a-zA-Z0-9\s]+$/',
            "service_category_id" => "required",
            /*'picture' => 'mimes:jpeg,jpg,bmp,png|max:5242880',
             'service_subcategory_order' => 'required|integer|between:0,10',*/
            "service_subcategory_status" => "required",
        ]);
        try {
            $subCategory = ServiceSubCategory::findOrFail($id);
            if ($subCategory) {
                $subCategory->service_subcategory_name =
                    $request->service_subcategory_name;
                $subCategory->service_category_id =
                    $request->service_category_id;
                $subCategory->service_subcategory_status =
                    $request->service_subcategory_status;
                $subCategory->save();
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
        // ONLY STATUS UPDATE ADDED INSTEAD OF HARD DELETE // return $this->removeModel($id);
        $subCategory = ServiceSubCategory::findOrFail($id);
        if ($subCategory) {
            $subCategory->service_subcategory_status = 2;
            $subCategory->save();
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
    public function categoriesList(): JsonResponse
    {
        $country = ServiceCategory::select(
            "id",
            "service_category_name",
            "service_category_status"
        )
            ->where("service_category_status", 1)
            ->get();
        return Helper::getResponse(["data" => $country]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $datum = ServiceSubCategory::findOrFail($id);

            if ($request->has("status") && $request->status == 1) {
                $datum->service_subcategory_status = 0;
            } else {
                $datum->service_subcategory_status = 1;
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

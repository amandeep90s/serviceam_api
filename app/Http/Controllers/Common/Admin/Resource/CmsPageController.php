<?php

namespace App\Http\Controllers\Common\Admin\Resource;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\CmsPage;
use App\Traits\Actions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CmsPageController extends Controller
{
    use Actions;

    private CmsPage $model;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CmsPage $model)
    {
        $this->model = $model;
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index(): JsonResponse
    {
        $cms_page = CmsPage::where(
            "company_id",
            Auth::user()->company_id
        )->get();

        return Helper::getResponse(["data" => $cms_page]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            "page_name" => "required",
            "content" => "required",
            "status" => "required",
        ]);

        $data = $request->all();

        try {
            $cms_page = new CmsPage();
            $cms_page->company_id = Auth::user()->company_id;
            $cms_page->page_name = $data['page_name'];
            $cms_page->content = $data['content'];
            $cms_page->status = $data['status'];
            $cms_page->save();

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
    public function show($page): JsonResponse
    {
        try {
            $cms_page = CmsPage::where("company_id", Auth::user()->company_id)
                ->where("page_name", $page)
                ->get();

            return Helper::getResponse(["data" => $cms_page]);
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
     * @throws ValidationException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->validate($request, [
            "page_name" => "required",
            "content" => "required",
            "status" => "required",
        ]);

        $data = $request->all();

        try {
            $cms_page = CmsPage::findOrFail($id);
            $cms_page->page_name = $data['page_name'];
            $cms_page->content = $data['content'];
            $cms_page->status = $data['status'];
            $cms_page->save();

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
    public function destroy($id): JsonResponse
    {
        return $this->removeModel($id);
    }
}

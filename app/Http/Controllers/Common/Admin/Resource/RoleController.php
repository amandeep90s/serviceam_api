<?php

namespace App\Http\Controllers\Common\Admin\Resource;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Traits\Actions;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use Actions;

    public function index(Request $request): JsonResponse
    {
        if ($request->has("search_text") && $request->search_text != null) {
            $datum = Role::where([
                "company_id" => null,
                "name" => $request->search_text,
            ])->orwhere([
                "company_id" => Auth::user()->company_id,
                "name" => $request->search_text,
            ]);
            $datum->where("name", $request->search_text);
        } else {
            $datum = Role::where("company_id", "=", null)->orwhere(
                "company_id",
                Auth::user()->company_id
            );
        }

        if ($request->has("order_by")) {
            $datum->orderby($request->order_by, $request->order_direction);
        }
        $data = $datum->paginate(10);

        return Helper::getResponse(["data" => $data]);
    }

    public function permission(): JsonResponse
    {
        $permissions = Permission::get();
        return Helper::getResponse(["data" => $permissions]);
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            "name" => "required|unique:roles,name",
            "permission" => "required",
        ]);

        try {
            $role = Role::create([
                "name" => STRTOUPPER($request->input("name")),
                "company_id" => Auth::user()->company_id,
            ]);
            $role->syncPermissions($request->input("permission"));

            return Helper::getResponse([
                "status" => 200,
                "message" => trans("admin.roles.saved"),
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
            $role = Role::findOrFail($id);
            $role->permissions = Permission::get();
            $role->UserPermissions = DB::table("role_has_permissions")
                ->where("role_has_permissions.role_id", $id)
                ->pluck(
                    "role_has_permissions.permission_id",
                    "role_has_permissions.permission_id"
                )
                ->all();
            return Helper::getResponse(["data" => $role]);
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
            "name" => "required",
            "permission" => "required",
        ]);

        try {
            $role = Role::find($id);
            $role->name = STRTOUPPER($request->input("name"));
            $role->save();
            $role->syncPermissions($request->input("permission"));

            return Helper::getResponse([
                "status" => 200,
                "message" => trans("admin.roles.updated"),
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
            Role::find($id)->delete();
            return Helper::getResponse([
                "status" => 200,
                "message" => trans("admin.roles.deleted"),
            ]);
        } catch (Exception $e) {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }
}

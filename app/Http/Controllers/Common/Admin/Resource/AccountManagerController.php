<?php

namespace App\Http\Controllers\Common\Admin\Resource;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\Admin;
use App\Traits\Actions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class AccountManagerController extends Controller
{
    use Actions;

    private Admin $model;
    private $request;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Admin $model)
    {
        $this->model = $model;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $datum = Admin::where("id", "!=", 1);

        $column_name = $datum->first()->toArray();

        $columns = !empty($column_name) ? array_keys($column_name) : [];

        if ($request->has("search_text") && $request->search_text != null) {
            $datum->where(function ($query) use ($columns, $request) {
                foreach ($columns as $column) {
                    $query->orWhere(
                        $column,
                        "LIKE",
                        "%" . $request->search_text . "%"
                    );
                }
            });
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
            "name" => "required|max:255",
            "email" =>
            $request->email != null
                ? "sometimes|required|unique:accounts,email|email|max:255"
                : "",
            "password" => "required|min:6|confirmed",
        ]);

        try {
            $request->request->add(["company_id" => Auth::user()->company_id]);
            $account = $request->all();
            $account["password"] = Hash::make($request->password);

            $account = Admin::create($account);

            $role = Role::where("name", "ACCOUNT")->first();

            if ($role != null) {
                $account->assignRole($role->id);
            }

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
            $account = Admin::findOrFail($id);
            return Helper::getResponse(["data" => $account]);
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
     * @throws ValidationException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->validate($request, [
            "name" => "required|max:255",
            "email" => "required|unique:dispatchers,email|email|max:255",
        ]);

        try {
            $account = Admin::findOrFail($id);
            $account->name = $request->name;
            $account->email = $request->email;
            if ($request->has("password")) {
                $account->password = $request->password;
            }
            $account->save();

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
        return $this->removeModel($id);
    }
}

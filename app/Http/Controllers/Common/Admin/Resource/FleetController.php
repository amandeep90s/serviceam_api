<?php

namespace App\Http\Controllers\Common\Admin\Resource;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\Admin;
use App\Models\Common\AdminCard;
use App\Models\Common\CompanyCity;
use App\Models\Common\CompanyCountry;
use App\Models\Common\FleetWallet;
use App\Models\Common\Setting;
use App\Models\Common\User;
use App\Models\Common\Zone;
use App\Traits\Actions;
use App\Traits\Encryptable;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Stripe\Customer;

class FleetController extends Controller
{
    use Actions;
    use Encryptable;

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
     *
     */
    public function index(Request $request): JsonResponse
    {
        $datum = Admin::where("company_id", Auth::user()->company_id)->where(
            "type",
            "FLEET"
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
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            "name" => "required|max:255",
            "company_name" => "required|max:255",
            "email" => "required|email|max:255",
            "mobile" => "digits_between:6,13",
            "picture" => "mimes:jpeg,jpg,bmp,png|max:5242880",
            "password" => "required|min:6|confirmed",
            "country_id" => "required",
            "city_id" => "required",
        ]);

        $request->merge([
            "email" => $this->customEncrypt($request->email, config('app.db_secret')),
            "mobile" => $this->customEncrypt($request->mobile, config('app.db_secret')),
        ]);

        $company_id = Auth::user()->company_id;
        $email = $request->email;
        $mobile = $request->mobile;

        $this->validate($request, [
            "email" => [
                Rule::unique("admins")->where(function ($query) use ($email, $company_id) {
                    return $query
                        ->where("email", $email)
                        ->where("company_id", $company_id)
                        ->where("type", "FLEET");
                }),
            ],
            "mobile" => [
                Rule::unique("admins")->where(function ($query) use ($mobile, $company_id) {
                    return $query
                        ->where("mobile", $mobile)
                        ->where("company_id", $company_id)
                        ->where("type", "FLEET");
                }),
            ],
        ]);

        try {
            $request->merge([
                "email" => $this->customDecrypt($request->email, config('app.db_secret')),
                "mobile" => $this->customDecrypt(
                    $request->mobile,
                    config('app.db_secret')
                ),
            ]);

            $request->request->add(["company_id" => Auth::user()->company_id]);
            $request->request->add(["parent_id" => Auth::user()->id]);
            $request->request->add(["type" => "FLEET"]);
            $fleet = $request->all();
            $fleet["password"] = Hash::make($request->password);
            if ($request->hasFile("picture")) {
                $fleet["picture"] = Helper::uploadFile(
                    $request->file("picture"),
                    "admin/logo"
                );
            }
            $country = CompanyCountry::where(
                "company_id",
                Auth::user()->company_id
            )
                ->where("country_id", $request->country_id)
                ->first();
            $fleet["currency_symbol"] = $country->currency;
            $fleet = Admin::create($fleet);

            $role = Role::where("name", "FLEET")->first();

            if ($role != null) {
                $fleet->assignRole($role->id);
            }

            $fleet->save();

            $request->merge(["body" => "registered"]);
            $this->sendUserData($request->all());

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
    public function show($id): JsonResponse
    {
        try {
            $fleet = Admin::findOrFail($id);
            $fleet["city_data"] = CompanyCity::where(
                "country_id",
                $fleet["country_id"]
            )
                ->with("city")
                ->get();
            $fleet["zone_data"] = Zone::where("city_id", $fleet["city_id"])
                ->where("company_id", Auth::user()->company_id)
                ->where("user_type", "SHOP")
                ->get();

            return Helper::getResponse(["data" => $fleet]);
        } catch (\Throwable $e) {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $datum = Admin::findOrFail($id);
            if ($request->has("status")) {
                if ($request->status == 1) {
                    $datum->status = 0;
                } else {
                    $datum->status = 1;
                }
            }
            $datum->save();
            if ($request->status == 1) {
                $status = "disabled";
            } else {
                $status = "enabled";
            }

            $datum["body"] = $status;
            $this->sendUserData($datum);

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

    /**
     * Remove the specified resource from storage.
     * @throws Exception
     */
    public function destroy($id): JsonResponse
    {
        $datum = Admin::findOrFail($id);

        $datum["body"] = "deleted";
        $this->sendUserData($datum);

        return $this->removeModel($id);
    }

    /**
     * @throws ValidationException
     */
    public function addcard(Request $request): JsonResponse
    {
        $this->validate($request, ["stripe_token" => "required"]);

        try {
            $customer_id = $this->customer_id();
            $this->set_stripe();
            $customer = Customer::retrieve($customer_id);
            $card = $customer->sources->create([
                "source" => $request->stripe_token,
            ]);

            $exist = AdminCard::where("admin_id", Auth::user()->id)
                ->where("last_four", $card->last4)
                ->where("brand", $card->brand)
                ->count();

            if ($exist == 0) {
                $create_card = new AdminCard();
                $create_card->admin_id = Auth::user()->id;
                $create_card->card_id = $card->id;
                $create_card->last_four = $card->last4;
                $create_card->brand = $card->brand;
                $create_card->company_id = Auth::user()->company_id;
                $create_card->month = $card->exp_month;
                $create_card->year = $card->exp_year;
                $create_card->holder_name = $card->name;
                $create_card->funding = $card->funding;
                $create_card->save();
            } else {
                return Helper::getResponse([
                    "status" => 403,
                    "message" => trans("api.card_already"),
                ]);
            }
            return Helper::getResponse(["message" => trans("api.card_added")]);
        } catch (Exception $e) {
            return Helper::getResponse([
                "status" => 500,
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get a stripe customer id.
     */
    public function customer_id()
    {
        if (Auth::user()->stripe_cust_id != null) {
            return Auth::user()->stripe_cust_id;
        } else {
            try {
                $stripe = $this->set_stripe();
                $customer = Customer::create([
                    "email" => Auth::user()->email,
                ]);

                User::where("id", Auth::user()->id)->update([
                    "stripe_cust_id" => $customer["id"],
                ]);
                return $customer["id"];
            } catch (Exception $e) {
                return $e;
            }
        }
    }

    /**
     * setting stripe.
     */
    public function set_stripe()
    {
        $settings = json_decode(
            json_encode(
                Setting::where("company_id", Auth::user()->company_id)->first()
                    ->settings_data
            )
        );

        $paymentConfig = json_decode(json_encode($settings->payment), true);

        $cardObject = array_values(
            array_filter($paymentConfig, function ($e) {
                return $e["name"] == "card";
            })
        );

        $stripe_secret_key = "";

        if (!empty($cardObject)) {
            $card = $cardObject[0]["status"];

            $stripeSecretObject = array_values(
                array_filter($cardObject[0]["credentials"], function ($e) {
                    return $e["name"] == "stripe_secret_key";
                })
            );
            $stripePublishableObject = array_values(
                array_filter($cardObject[0]["credentials"], function ($e) {
                    return $e["name"] == "stripe_publishable_key";
                })
            );
            $stripeCurrencyObject = array_values(
                array_filter($cardObject[0]["credentials"], function ($e) {
                    return $e["name"] == "stripe_currency";
                })
            );

            if (!empty($stripeSecretObject)) {
                $stripe_secret_key = $stripeSecretObject[0]["value"];
            }

            if (!empty($stripePublishableObject)) {
                $stripe_publishable_key = $stripePublishableObject[0]["value"];
            }

            if (!empty($stripeCurrencyObject)) {
                $stripe_currency = $stripeCurrencyObject[0]["value"];
            }
        }

        return \Stripe\Stripe::setApiKey($stripe_secret_key);
    }

    /**
     * Update the specified resource in storage.
     * @throws ValidationException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->validate($request, [
            "name" => "required|max:255",
            "company_name" => "required|max:255",
            "mobile" => "digits_between:6,13",
            "picture" => "mimes:jpeg,jpg,bmp,png|max:5242880",
            "country_id" => "required",
            "city_id" => "required",
            "zone_id" => "required",
        ]);
        if ($request->has("email")) {
            $request->merge([
                "email" => $this->customEncrypt($request->email, config('app.db_secret')),
            ]);
        }
        if ($request->has("mobile")) {
            $request->merge([
                "mobile" => $this->customEncrypt(
                    $request->mobile,
                    config('app.db_secret')
                ),
            ]);
        }

        $company_id = Auth::user()->company_id;
        if ($request->has("email")) {
            $email = $request->email;
        }
        if ($request->has("mobile")) {
            $mobile = $request->mobile;
        }
        if ($request->has("email")) {
            $this->validate($request, [
                "email" => [
                    Rule::unique("admins")->where(function ($query) use ($email, $company_id, $id) {
                        return $query
                            ->where("email", $email)
                            ->where("company_id", $company_id)
                            ->where("type", "FLEET")
                            ->whereNotIn("id", [$id]);
                    }),
                ],
            ]);
        }
        if ($request->has("mobile")) {
            $this->validate($request, [
                "mobile" => [
                    Rule::unique("admins")->where(function ($query) use ($mobile, $company_id, $id) {
                        return $query
                            ->where("mobile", $mobile)
                            ->where("company_id", $company_id)
                            ->where("type", "FLEET")
                            ->whereNotIn("id", [$id]);
                    }),
                ],
            ]);
        }

        try {
            if ($request->has("email")) {
                $request->merge([
                    "email" => $this->customDecrypt(
                        $request->email,
                        config('app.db_secret')
                    ),
                ]);
            }
            if ($request->has("mobile")) {
                $request->merge([
                    "mobile" => $this->customDecrypt(
                        $request->mobile,
                        config('app.db_secret')
                    ),
                ]);
            }

            $fleet = Admin::findOrFail($id);
            if ($request->hasFile("picture")) {
                $fleet->picture = Helper::uploadFile(
                    $request->file("picture"),
                    "admin/logo"
                );
            }
            $fleet->name = $request->name;
            $fleet->company_name = $request->company_name;
            if ($request->has("mobile")) {
                $fleet->mobile = $request->mobile;
                $fleet->country_id = $request->country_id;
            }
            if ($request->has("email")) {
                $fleet->email = $request->email;
            }

            $fleet->city_id = $request->city_id;
            $fleet->commision = $request->commision;
            $fleet->zone_id = $request->zone_id;
            $country = CompanyCountry::where(
                "company_id",
                Auth::user()->company_id
            )
                ->where("country_id", $request->country_id)
                ->first();
            $fleet->currency_symbol = $country->currency;

            $fleet->save();

            $request->merge(["body" => "updated"]);
            if ($request->has("email")) {
                $this->sendUserData($request->all());
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

    public function card(Request $request): JsonResponse
    {
        $datum = AdminCard::where("company_id", Auth::user()->company_id);
        if ($request->has("search_text") && $request->search_text != null) {
            $datum->Search($request->search_text);
        }
        if ($request->has("order_by")) {
            $datum->orderby($request->order_by, $request->order_direction);
        }
        $data = $datum->paginate(10);
        return Helper::getResponse(["data" => $data]);
    }

    public function wallet(Request $request): JsonResponse
    {
        $datum = FleetWallet::with("admin_service")
            ->where("company_id", Auth::user()->company_id)
            ->where("fleet_id", Auth::user()->id);
        if ($request->has("search_text") && $request->search_text != null) {
            $datum->Search($request->search_text);
        }
        if ($request->has("order_by")) {
            $datum->orderby($request->order_by, $request->order_direction);
        }
        $data = $datum->paginate(10);
        return Helper::getResponse(["data" => $data]);
    }
}

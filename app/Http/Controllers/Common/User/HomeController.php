<?php

namespace App\Http\Controllers\Common\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\Card;
use App\Models\Common\Chat;
use App\Models\Common\City;
use App\Models\Common\CompanyCity;
use App\Models\Common\CompanyCountry;
use App\Models\Common\Menu;
use App\Models\Common\Notification;
use App\Models\Common\Promocode;
use App\Models\Common\Reason;
use App\Models\Common\Setting;
use App\Models\Common\State;
use App\Models\Common\User;
use App\Models\Common\UserAddress;
use App\Models\Common\UserRequest;
use App\Models\Common\UserWallet;
use App\Models\Service\ServiceRequest;
use App\Services\ReferralResource;
use App\Traits\Encryptable;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Stripe\Customer;

class HomeController extends Controller
{
    use Encryptable;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::guard("user")->user();

        $menus = new \stdClass();
        $recent = [];

        $recentServiceRequest = ServiceRequest::select(
            "service_category_id",
            DB::raw('MAX(id) as max_id')
        )
            ->where("user_id", $user->id)
            ->groupBy("service_category_id")
            ->orderBy('max_id', 'desc')
            ->limit(5)
            ->get();

        $recent_data = [];

        if (!empty($recentServiceRequest)) {
            foreach ($recentServiceRequest as $key => $value) {
                $recent_data[$key] = $value->service_category_id;
            }
            $recent = Menu::whereIn("menu_type_id", $recent_data)
                ->where("company_id", $user->company_id)
                ->where("status", 1)
                ->orderby("sort_order")
                ->get();
        }

        $menus->services = Menu::with("service")
            ->whereHas("cities", function ($query) use ($user) {
                $query->where("city_id", $user->city_id);
            })
            ->where("company_id", $user->company_id)
            ->where("status", 1)
            ->orderby("sort_order")
            ->get();
        $menus->promocodes = Promocode::where(
            "company_id",
            Auth::guard("user")->user()->company_id
        )
            ->where("expiration", ">=", date("Y-m-d H:i"))
            ->whereDoesntHave("promousage", function ($query) {
                $query->where("user_id", Auth::guard("user")->user()->id);
            })
            ->get();
        $menus->recent_requests = $recent;

        return Helper::getResponse(["data" => $menus]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function search_user(Request $request): JsonResponse
    {
        $results = [];
        $term = $request->input("stext");
        $queries = User::where("company_id", Auth::user()->company_id)
            ->where(function ($query) use ($term) {
                $query
                    ->where("first_name", "LIKE", $term . "%")
                    ->orWhere("last_name", "LIKE", $term . "%");
            })
            ->take(5)
            ->get();
        foreach ($queries as $query) {
            $results[] = $query;
        }
        return response()->json(["success" => true, "data" => $results]);
    }

    public function city(Request $request): JsonResponse
    {
        try {
            User::where(
                "company_id",
                Auth::guard("user")->user()->company_id
            )
                ->where("id", Auth::guard("user")->user()->id)
                ->update(["city_id" => $request["city_id"]]);
            return Helper::getResponse([
                "status" => 200,
                "message" => "Updated Successfully",
            ]);
        } catch (\Throwable $e) {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    public function defaultcard(Request $request): JsonResponse
    {
        try {
            $card = Card::where("card_id", $request->card_id)->get();
            if (!empty($card)) {
                Card::where("user_id", Auth::guard("user")->user()->id)->update(
                    ["is_default" => 0]
                );
                Card::where("card_id", $request->card_id)->update([
                    "is_default" => 1,
                ]);

                return Helper::getResponse([
                    "status" => 200,
                    "message" => trans("admin.update"),
                ]);
            } else {
                return Helper::getResponse([
                    "status" => 200,
                    "message" => "Card Not Exist",
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

    public function listpromocode($service = null): JsonResponse
    {
        $type = strtoupper($service);

        $data = Promocode::where(
            "company_id",
            Auth::guard("user")->user()->company_id
        )
            ->where("service", $type)
            ->get();

        return Helper::getResponse(["data" => $data]);
    }

    /**
     * @throws ValidationException
     */
    public function get_chat(Request $request): JsonResponse
    {
        $this->validate($request, [
            "admin_service" => "required|in:TRANSPORT,ORDER,SERVICE",
            "id" => "required",
        ]);

        $chat = Chat::where("admin_service", $request->admin_service)
            ->where("request_id", $request->id)
            ->where("company_id", Auth::guard("user")->user()->company_id)
            ->get();

        return Helper::getResponse(["data" => $chat]);
    }

    /**
     * @throws ValidationException
     */
    public function updateDeviceToken(Request $request): JsonResponse
    {
        $this->validate($request, [
            "device_token" => "required",
        ]);
        try {
            $user_id = Auth::guard("user")->user()->id;
            $update = User::where("id", $user_id)->update([
                "device_token" => $request->device_token,
            ]);

            return Helper::getResponse([
                "status" => 200,
                "message" => trans("admin.update"),
            ]);
        } catch (ModelNotFoundException $e) {
            return Helper::getResponse([
                "status" => 500,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    public function ongoing_services(Request $request): JsonResponse
    {
        try {
            $requests = UserRequest::with("service")
                ->where("user_id", Auth::guard("user")->user()->id)
                ->whereNotIn("status", ["SCHEDULED", "CANCELLED"])
                ->get();

            return Helper::getResponse(["data" => $requests]);
        } catch (Exception $e) {
            return response()->json(
                ["error" => trans("api.something_went_wrong")],
                500
            );
        }
    }

    /**
     * @throws ValidationException
     */
    public function addManageAddress(Request $request): JsonResponse
    {
        $this->validate($request, [
            "map_address" => "required",
            "address_type" => "required",
            "latitude" => "required",
            "longitude" => "required",
            "flat_no" => "required",
            "street" => "required",
        ]);

        try {
            $title =
                $request->address_type == "Home" ||
                $request->address_type == "Work"
                ? $request->address_type
                : (!empty($request->title)
                    ? $request->title
                    : "Other");

            $UserAddress = UserAddress::where(
                "company_id",
                Auth::guard("user")->user()->company_id
            )
                ->where("user_id", Auth::guard("user")->user()->id)
                ->where("address_type", $request->address_type)
                ->where("title", $title)
                ->first();

            if ($UserAddress != null) {
                $userAddress = $UserAddress;
            } else {
                $userAddress = new UserAddress();
            }

            $userAddress->address_type = $request->address_type;
            $userAddress->company_id = Auth::guard("user")->user()->company_id;
            $userAddress->user_id = Auth::guard("user")->user()->id;
            $userAddress->landmark = $request->landmark;
            $userAddress->flat_no = $request->flat_no;
            $userAddress->title = $title;
            $userAddress->street = $request->street;
            $userAddress->latitude = $request->latitude;
            $userAddress->longitude = $request->longitude;
            $userAddress->map_address = $request->map_address;
            $userAddress->city = $request->city;
            $userAddress->state = $request->state;
            $userAddress->county = $request->county;
            $userAddress->zipcode = $request->zipcode;
            $userAddress->save();
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

    public function editManageAddress(Request $request, $id): JsonResponse
    {
        $userAddressDetails = UserAddress::find($id);
        return Helper::getResponse([
            "status" => 200,
            "data" => $userAddressDetails,
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function updateManageAddress(Request $request): JsonResponse
    {
        $this->validate($request, [
            "address_type" => "required",
            "latitude" => "required",
            "longitude" => "required",
            "flat_no" => "required",
            "street" => "required",
        ]);
        try {
            $userAddress = UserAddress::findOrFail($request->id);
            $userAddress->address_type = $request->address_type;
            $userAddress->landmark = $request->landmark;
            $userAddress->flat_no = $request->flat_no;
            $userAddress->street = $request->street;
            $userAddress->latitude = $request->latitude;
            $userAddress->longitude = $request->longitude;
            $userAddress->city = $request->city;
            $userAddress->state = $request->state;
            $userAddress->county = $request->county;
            $userAddress->zipcode = $request->zipcode;
            $userAddress->map_address = $request->map_address;
            $userAddress->save();
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

    public function listManageAddress(Request $request): JsonResponse
    {
        try {
            $userAddressDetails = UserAddress::where(
                "user_id",
                Auth::guard("user")->user()->id
            )->get();
            return Helper::getResponse([
                "status" => 200,
                "data" => $userAddressDetails,
            ]);
        } catch (\Throwable $e) {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    public function deleteManageAddress($id): JsonResponse
    {
        try {
            UserAddress::where("id", $id)->delete();
            return Helper::getResponse([
                "message" => trans("admin.user_msgs.user_delete"),
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
     * @throws ValidationException
     */
    public function updatelanguage(Request $request): JsonResponse
    {
        $this->validate($request, [
            "language" => "required",
        ]);
        try {
            $user = User::findOrFail(Auth::guard("user")->user()->id);
            $user->language = $request->language;
            $user->save();
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

    public function show_profile(): JsonResponse
    {
        $user_details = User::with("country", "state", "city")
            ->where("id", Auth::guard("user")->user()->id)
            ->where("company_id", Auth::guard("user")->user()->company_id)
            ->first();
        $user_details["referral"] = (object)[];

        $settings = json_decode(
            json_encode(
                Setting::where(
                    "company_id",
                    Auth::guard("user")->user()->company_id
                )->first()->settings_data
            )
        );
        if ($settings->site->referral == 1) {
            $user_details["referral"]->referral_code = $user_details["referral_unique_id"];
            $user_details["referral"]->referral_amount = (float)$settings->site->referral_amount;
            $user_details["referral"]->referral_count = (int)$settings->site->referral_count;
            $user_details["referral"]->user_referral_count = (int)$user_details->referal_count;
            $user_details["referral"]->user_referral_amount = (new ReferralResource())->get_referral(1, Auth::guard("user")->user()->id)[0]->total_amount;
        }
        return Helper::getResponse(["data" => $user_details]);
    }

    /**
     * @throws ValidationException
     */
    public function updateProfile(Request $request): JsonResponse
    {
        if ($request->has("mobile")) {
            $request->merge([
                "mobile" => $this->customEncrypt(
                    $request->mobile,
                    config('app.db_secret')
                ),
            ]);
            $mobile = $request->mobile;
            $company_id = Auth::guard("user")->user()->company_id;
            $id = Auth::guard("user")->user()->id;

            $this->validate($request, [
                "mobile" => [
                    Rule::unique("users")->where(function ($query) use ($mobile, $company_id, $id) {
                        return $query
                            ->where("mobile", $mobile)
                            ->where("company_id", $company_id)
                            ->whereNotIn("id", [$id]);
                    }),
                ],
            ]);

            $request->merge([
                "mobile" => $this->customDecrypt(
                    $request->mobile,
                    config('app.db_secret')
                ),
            ]);
        }

        try {
            $user = User::where("id", Auth::guard("user")->user()->id)
                ->where("company_id", Auth::guard("user")->user()->company_id)
                ->first();
            $user->first_name = $request->first_name;
            if ($request->has("last_name")) {
                $user->last_name = $request->last_name;
            }
            if ($request->has("email")) {
                $user->email = $request->email;
            }

            if ($request->has("language")) {
                $user->language = $request->language;
            }
            if ($request->has("mobile")) {
                $user->mobile = $request->mobile;
            }

            if ($request->has("city_id")) {
                $user->city_id = $request->city_id;
            }
            if ($request->has("country_code")) {
                $user->country_code = $request->country_code;
            }
            if ($request->has("gender")) {
                $user->gender = $request->gender;
            }
            if ($request->hasFile("picture")) {
                $user->picture = Helper::uploadFile(
                    $request->file("picture"),
                    "user",
                    null,
                    Auth::guard("user")->user()->company_id
                );
            }
            $user->save();
            return Helper::getResponse([
                "status" => 200,
                "message" => trans("admin.update"),
                "data" => $user,
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
     * @throws ValidationException
     */
    public function passwordUpdate(Request $request): JsonResponse
    {
        $this->validate(
            $request,
            [
                "old_password" => "required",
                "password" => "required|min:6|different:old_password",
                "password_confirmation" => "required",
            ],
            [
                "password.different" =>
                "The new password and old password should not be same",
            ]
        );

        try {
            $user = User::where("id", Auth::guard("user")->user()->id)
                ->where("company_id", Auth::guard("user")->user()->company_id)
                ->first();
            if (password_verify($request->old_password, $user->password)) {
                $user->password = Hash::make($request->password);
                $user->save();
                return Helper::getResponse([
                    "status" => 200,
                    "message" => trans("admin.update"),
                ]);
            } else {
                return Helper::getResponse([
                    "status" => 422,
                    "message" => trans("admin.old_password_incorrect"),
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
     * @throws ValidationException
     */
    public function addCard(Request $request): JsonResponse
    {
        $this->validate($request, [
            "stripe_token" => "required",
        ]);

        try {
            $customer_id = $this->customer_id();
            $this->set_stripe();
            $customer = Customer::retrieve($customer_id);
            $card = $customer->createSource($customer->id, [
                "source" => $request->stripe_token,
            ]);

            $user = Auth::guard("user")->user();

            $exist = Card::where("user_id", $user->id)
                ->where("last_four", $card->last4)
                ->where("brand", $card->brand)
                ->count();

            if ($exist == 0) {
                $create_card = new Card();
                $create_card->user_id = $user->id;
                $create_card->card_id = $card->id;
                $create_card->last_four = $card->last4;
                $create_card->brand = $card->brand;
                $create_card->company_id = $user->company_id;
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

            return Helper::getResponse([
                "status" => 200,
                "data" => $create_card,
                "message" => trans("api.card_added"),
            ]);
        } catch (Exception $e) {
            return Helper::getResponse([
                "status" => 500,
                "error" => $e->getMessage(),
            ]);
        }
    }

    public function customer_id()
    {
        if (Auth::guard("user")->user()->stripe_cust_id != null) {
            return Auth::guard("user")->user()->stripe_cust_id;
        } else {
            try {
                $this->set_stripe();
                $customer = Customer::create([
                    "email" => Auth::guard("user")->user()->email,
                ]);

                User::where("id", Auth::guard("user")->user()->id)->update([
                    "stripe_cust_id" => $customer["id"],
                ]);
                return $customer["id"];
            } catch (Exception $e) {
                return $e;
            }
        }
    }

    public function set_stripe()
    {
        $settings = json_decode(
            json_encode(
                Setting::where(
                    "company_id",
                    Auth::guard("user")->user()->company_id
                )->first()->settings_data
            )
        );

        $paymentConfig = json_decode(json_encode($settings->payment), true);

        $cardObject = array_values(
            array_filter($paymentConfig, function ($e) {
                return $e["name"] == "card";
            })
        );
        $card = 0;

        $stripe_secret_key = "";
        $stripe_publishable_key = "";
        $stripe_currency = "";

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

    public function cardDetail(Request $request): JsonResponse
    {
        $cards = Card::where("user_id", Auth::guard("user")->user()->id)
            ->where("company_id", Auth::guard("user")->user()->company_id)
            ->get();
        return Helper::getResponse(["data" => $cards]);
    }

    public function deleteCard(Request $request, $id): JsonResponse
    {
        $card = Card::where("id", $id)->first();
        if ($card) {
            try {
                Card::where("id", $id)->delete();
                return Helper::getResponse([
                    "status" => 200,
                    "message" => "Card Deleted",
                ]);
            } catch (Exception $e) {
                return Helper::getResponse([
                    "status" => 422,
                    "message" => "Card Not Found",
                    "error" => $e->getMessage(),
                ]);
            }
        } else {
            return Helper::getResponse([
                "status" => 422,
                "message" => "Card Not Found",
            ]);
        }
    }

    public function userList(): JsonResponse
    {
        $user_list = User::where("id", Auth::guard("user")->user()->id)
            ->where("company_id", Auth::guard("user")->user()->company_id)
            ->with("country")
            ->first();
        return Helper::getResponse(["data" => $user_list]);
    }

    public function walletList(Request $request): JsonResponse
    {
        if ($request->has("limit")) {
            $user_wallet = UserWallet::select(
                "id",
                "transaction_id",
                "transaction_desc",
                "transaction_alias",
                "type",
                "amount",
                "created_at"
            )
                ->with([
                    "payment_log" => function ($query) {
                        $query->select(
                            "id",
                            "company_id",
                            "is_wallet",
                            "user_type",
                            "payment_mode",
                            "user_id",
                            "amount",
                            "transaction_code"
                        );
                    },
                ])
                ->where("company_id", Auth::guard("user")->user()->company_id)
                ->where("user_id", Auth::guard("user")->user()->id)
                ->orderBy("id", "desc");
            $totalRecords = $user_wallet->count();
            $user_wallet = $user_wallet
                ->take($request->limit)
                ->offset($request->offset)
                ->get();
            $response["total_records"] = $totalRecords;
            $response["data"] = $user_wallet;
            return Helper::getResponse(["data" => $response]);
        } else {
            $user_wallet = UserWallet::select(
                "id",
                "user_id",
                "transaction_id",
                "transaction_alias",
                "transaction_desc",
                "type",
                "amount",
                "created_at"
            )
                ->with([
                    "payment_log" => function ($query) {
                        $query->select(
                            "id",
                            "company_id",
                            "is_wallet",
                            "user_type",
                            "payment_mode",
                            "user_id",
                            "amount",
                            "transaction_code"
                        );
                    },
                    "user" => function ($query) {
                        $query->select("id", "currency_symbol");
                    },
                ])
                ->where("company_id", Auth::guard("user")->user()->company_id)
                ->where("user_id", Auth::guard("user")->user()->id);
            if ($request->has("search_text") && $request->search_text != null) {
                $user_wallet->Search($request->search_text);
            }

            if ($request->has("order_by")) {
                $user_wallet->orderby(
                    $request->order_by,
                    $request->order_direction
                );
            }
            $user_wallet = $user_wallet->paginate(10);
        }
        return Helper::getResponse(["data" => $user_wallet]);
    }

    public function order_status(Request $request): JsonResponse
    {
        $order_status = UserRequest::where(
            "user_id",
            Auth::guard("user")->user()->id
        )
            ->whereNotIn("status", ["CANCELLED", "SCHEDULED"])
            ->get();
        return Helper::getResponse(["data" => $order_status]);
    }

    public function countries(Request $request): JsonResponse
    {
        $company_id = base64_decode($request->salt_key);
        $country_list = CompanyCountry::with([
            "companyCountryCities" => function ($q) use ($company_id) {
                $q->where("company_id", $company_id);
            },
        ])
            ->has("companyCountryCities")
            ->where("company_id", $company_id)
            ->where("status", 1)
            ->get();
        $countries = [];
        foreach ($country_list as $country) {
            $object = new \stdClass();
            $object->id = $country->country->id;
            $object->country_name = $country->country->country_name;
            $object->country_code = $country->country->country_code;
            $object->country_phonecode = $country->country->country_phonecode;
            $object->country_currency = $country->country->country_currency;
            $object->country_symbol = $country->country->country_symbol;
            $object->status = $country->country->status;
            $object->timezone = $country->country->timezone;
            foreach ($country->companyCountryCities as $value) {
                $object->city[] = $value->city;
            }
            $countries[] = $object;
        }

        return Helper::getResponse(["data" => $countries]);
    }

    public function cities(Request $request): JsonResponse
    {
        $company_cities = CompanyCity::where(
            "company_id",
            Auth::guard("user")->user()->company_id
        )
            ->where("country_id", Auth::guard("user")->user()->country_id)
            ->pluck("city_id")
            ->all();

        $cities = City::whereIn("id", $company_cities)->get();
        return Helper::getResponse(["data" => $cities]);
    }

    public function promocode(Request $request): JsonResponse
    {
        $promocode = Promocode::where(
            "company_id",
            Auth::guard("user")->user()->company_id
        )
            ->whereDate("expiration", ">=", Carbon::today())
            ->orderby("id", "desc")
            ->get();

        return Helper::getResponse(["data" => $promocode]);
    }

    public function reasons(Request $request): JsonResponse
    {
        $reason = Reason::where(
            "company_id",
            Auth::guard("user")->user()->company_id
        )
            ->where("service", $request->type)
            ->where("type", "USER")
            ->where("status", "active")
            ->get();

        return Helper::getResponse(["data" => $reason]);
    }

    public function notification(Request $request): JsonResponse
    {
        try {
            $timezone = Auth::guard("user")->user()->state_id
                ? State::find(Auth::guard("user")->user()->state_id)->timezone
                : "";
            $jsonResponse = [];
            if ($request->has("limit")) {
                $notifications = Notification::where(
                    "company_id",
                    Auth::guard("user")->user()->company_id
                )
                    ->where("notify_type", "!=", "provider")
                    ->where("status", "active")
                    ->whereDate("expiry_date", ">=", Carbon::today())
                    ->take($request->limit)
                    ->offset($request->offset)
                    ->orderby("id", "desc")
                    ->get();
            } else {
                $notifications = Notification::where(
                    "company_id",
                    Auth::guard("user")->user()->company_id
                )
                    ->where("notify_type", "!=", "provider")
                    ->where("status", "active")
                    ->whereDate("expiry_date", ">=", Carbon::today())
                    ->orderby("id", "desc")
                    ->paginate(10);
            }

            if (!empty($notifications)) {
                foreach ($notifications as $k => $val) {
                    $notifications[$k]["created_at"] = Carbon::createFromFormat(
                        "Y-m-d H:i:s",
                        $val["created_at"],
                        "UTC"
                    )
                        ->setTimezone($timezone)
                        ->format("Y-m-d H:i:s");
                }
            }

            $jsonResponse["total_records"] = count($notifications);
            $jsonResponse["notification"] = $notifications;
        } catch (\Exception $e) {
            return response()->json([
                "error" => trans("api.something_went_wrong"),
            ]);
        }
        return Helper::getResponse(["data" => $jsonResponse]);
    }
}

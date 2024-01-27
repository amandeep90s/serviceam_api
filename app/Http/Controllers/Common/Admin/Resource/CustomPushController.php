<?php

namespace App\Http\Controllers\Common\Admin\Resource;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\CustomPush;
use App\Models\Common\Provider;
use App\Models\Common\User;
use App\Models\Order\StoreOrder;
use App\Models\Service\ServiceRequest;
use App\Models\Transport\RideRequest;
use App\Services\SendPushNotification;
use App\Traits\Actions;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CustomPushController extends Controller
{
    use Actions;

    private CustomPush $model;
    private $request;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CustomPush $model)
    {
        $this->model = $model;
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index(): JsonResponse
    {
        $pushes = CustomPush::where(
            "company_id",
            Auth::user()->company_id
        )->paginate(10);

        return Helper::getResponse(["data" => $pushes]);
    }

    /**
     * pages.
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            "send_to" => "required|in:ALL,USERS,PROVIDERS",
            "user_condition" => [
                "required_if:send_to,USERS",
                "in:ACTIVE,LOCATION,RIDES,AMOUNT",
            ],
            "provider_condition" => [
                "required_if:send_to,PROVIDERS",
                "in:ACTIVE,LOCATION,RIDES,AMOUNT",
            ],
            "user_active" => [
                "required_if:user_condition,ACTIVE",
                "in:HOUR,WEEK,MONTH",
            ],
            "user_rides" => "required_if:user_condition,RIDES",
            "user_location" => "required_if:user_condition,LOCATION",
            "user_amount" => "required_if:user_condition,AMOUNT",
            "provider_active" => [
                "required_if:provider_condition,ACTIVE",
                "in:HOUR,WEEK,MONTH",
            ],
            "provider_rides" => "required_if:provider_condition,RIDES",
            "provider_location" => "required_if:provider_condition,LOCATION",
            "provider_amount" => "required_if:provider_condition,AMOUNT",
            "message" => "required|max:100",
        ]);

        try {
            $CustomPush = new CustomPush();
            $CustomPush->send_to = $request->send_to;
            $CustomPush->message = $request->message;
            $CustomPush->company_id = Auth::user()->company_id;
            if ($request->send_to == "USERS") {
                $CustomPush->condition = $request->user_condition;

                if ($request->user_condition == "ACTIVE") {
                    $CustomPush->condition_data = $request->user_active;
                } elseif ($request->user_condition == "LOCATION") {
                    $CustomPush->condition_data = $request->user_location;
                } elseif ($request->user_condition == "RIDES") {
                    $CustomPush->condition_data = $request->user_rides;
                } elseif ($request->user_condition == "AMOUNT") {
                    $CustomPush->condition_data = $request->user_amount;
                }
            } elseif ($request->send_to == "PROVIDERS") {
                $CustomPush->condition = $request->provider_condition;

                if ($request->provider_condition == "ACTIVE") {
                    $CustomPush->condition_data = $request->provider_active;
                } elseif ($request->provider_condition == "LOCATION") {
                    $CustomPush->condition_data = $request->provider_location;
                } elseif ($request->provider_condition == "RIDES") {
                    $CustomPush->condition_data = $request->provider_rides;
                } elseif ($request->provider_condition == "AMOUNT") {
                    $CustomPush->condition_data = $request->provider_amount;
                }
            }

            if (
                $request->has("schedule_date") &&
                $request->schedule_date != "" &&
                $request->has("schedule_time") &&
                $request->schedule_time != ""
            ) {
                $CustomPush->schedule_at = date(
                    "Y-m-d H:i:s",
                    strtotime("$request->schedule_date $request->schedule_time")
                );
            }
            $CustomPush->save();
            if ($CustomPush->schedule_at == "") {
                $this->SendCustomPush($CustomPush->id);
            }

            return Helper::getResponse([
                "status" => 200,
                "message" => trans("admin.create"),
            ]);
        } catch (\Throwable $e) {
            Log::info($e);
            return Helper::getResponse([
                "status" => 404,
                "message" => trans("admin.something_wrong"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    public function SendCustomPush($CustomPush)
    {
        try {

            $push = CustomPush::findOrFail($CustomPush);

            if ($push->send_to == "USERS") {
                $users = [];

                if ($push->condition == "ACTIVE") {
                    if ($push->condition_data == "HOUR") {
                        $ServiceRequest = ServiceRequest::where(
                            "created_at",
                            ">=",
                            Carbon::now()->subHour()
                        )
                            ->pluck("user_id", "user_id")
                            ->toArray();

                        $users = array_flip($ServiceRequest);
                    } elseif ($push->condition_data == "WEEK") {
                        $StoreOrder = StoreOrder::where(
                            "created_at",
                            ">=",
                            Carbon::now()->subWeek()
                        )
                            ->pluck("user_id", "user_id")
                            ->toArray();
                        $ServiceRequest = ServiceRequest::where(
                            "created_at",
                            ">=",
                            Carbon::now()->subWeek()
                        )
                            ->pluck("user_id", "user_id")
                            ->toArray();
                        $RideRequest = RideRequest::where(
                            "created_at",
                            ">=",
                            Carbon::now()->subWeek()
                        )
                            ->pluck("user_id", "user_id")
                            ->toArray();

                        $users = array_flip(
                            array_merge(
                                $StoreOrder,
                                $ServiceRequest,
                                $RideRequest
                            )
                        );
                    } elseif ($push->condition_data == "MONTH") {
                        $StoreOrder = StoreOrder::where(
                            "created_at",
                            ">=",
                            Carbon::now()->subMonth()
                        )
                            ->pluck("user_id", "user_id")
                            ->toArray();
                        $ServiceRequest = ServiceRequest::where(
                            "created_at",
                            ">=",
                            Carbon::now()->subMonth()
                        )
                            ->pluck("user_id", "user_id")
                            ->toArray();
                        $RideRequest = RideRequest::where(
                            "created_at",
                            ">=",
                            Carbon::now()->subMonth()
                        )
                            ->pluck("user_id", "user_id")
                            ->toArray();

                        $users = array_flip(
                            array_merge(
                                $StoreOrder,
                                $ServiceRequest,
                                $RideRequest
                            )
                        );
                    }
                } elseif ($push->condition == "RIDES") {
                    $ServiceRequest = ServiceRequest::select(
                        "id",
                        "user_id",
                        DB::raw("COUNT(id) as total_number")
                    )
                        ->groupBy("user_id")
                        ->get();

                    $total_order = collect($StoreOrder)
                        ->merge($ServiceRequest)
                        ->merge($RideRequest);
                    $total_users = [];
                    foreach ($total_order as $key => $val) {
                        if (@$total_users[$val->user_id] == "") {
                            $total_users[$val->user_id] = $val->total_number;
                        } else {
                            $total_users[$val->user_id] =
                                @$total_users[$val->user_id] +
                                $val->total_number;
                        }
                    }
                    foreach ($total_users as $key => $val) {
                        if ($val >= $push->condition_data) {
                            $users[$key] = $val;
                        }
                    }
                } elseif ($push->condition == "LOCATION") {
                    $Location = explode(",", $push->condition_data);

                    $distance = config(
                        "constants.provider_search_radius",
                        "10"
                    );
                    $latitude = $Location[0];
                    $longitude = $Location[1];

                    $users = User::whereRaw(
                        "(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance"
                    )
                        ->pluck("id", "id")
                        ->toArray();
                }

                foreach ($users as $key => $user) {
                    (new SendPushNotification())->sendPushToUser(
                        $key,
                        "",
                        $push->message
                    );
                }
            } elseif ($push->send_to == "PROVIDERS") {
                $Providers = [];

                if ($push->condition == "ACTIVE") {
                    if ($push->condition_data == "HOUR") {
                        $StoreOrder = StoreOrder::where(
                            "created_at",
                            ">=",
                            Carbon::now()->subHour()
                        )
                            ->whereNotNull("provider_id")
                            ->pluck("provider_id", "provider_id")
                            ->toArray();
                        $ServiceRequest = ServiceRequest::where(
                            "created_at",
                            ">=",
                            Carbon::now()->subHour()
                        )
                            ->whereNotNull("provider_id")
                            ->pluck("provider_id", "provider_id")
                            ->toArray();
                        $RideRequest = RideRequest::where(
                            "created_at",
                            ">=",
                            Carbon::now()->subHour()
                        )
                            ->whereNotNull("provider_id")
                            ->pluck("provider_id", "provider_id")
                            ->toArray();

                        $Providers = array_flip(
                            array_merge(
                                $StoreOrder,
                                $ServiceRequest,
                                $RideRequest
                            )
                        );
                    } elseif ($push->condition_data == "WEEK") {
                        $StoreOrder = StoreOrder::where(
                            "created_at",
                            ">=",
                            Carbon::now()->subWeek()
                        )
                            ->whereNotNull("provider_id")
                            ->pluck("provider_id", "provider_id")
                            ->toArray();
                        $ServiceRequest = ServiceRequest::where(
                            "created_at",
                            ">=",
                            Carbon::now()->subWeek()
                        )
                            ->whereNotNull("provider_id")
                            ->pluck("provider_id", "provider_id")
                            ->toArray();
                        $RideRequest = RideRequest::where(
                            "created_at",
                            ">=",
                            Carbon::now()->subWeek()
                        )
                            ->whereNotNull("provider_id")
                            ->pluck("provider_id", "provider_id")
                            ->toArray();

                        $Providers = array_flip(
                            array_merge(
                                $StoreOrder,
                                $ServiceRequest,
                                $RideRequest
                            )
                        );
                    } elseif ($push->condition_data == "MONTH") {
                        $StoreOrder = StoreOrder::where(
                            "created_at",
                            ">=",
                            Carbon::now()->subMonth()
                        )
                            ->whereNotNull("provider_id")
                            ->pluck("provider_id", "provider_id")
                            ->toArray();
                        $ServiceRequest = ServiceRequest::where(
                            "created_at",
                            ">=",
                            Carbon::now()->subMonth()
                        )
                            ->whereNotNull("provider_id")
                            ->pluck("provider_id", "provider_id")
                            ->toArray();
                        $RideRequest = RideRequest::where(
                            "created_at",
                            ">=",
                            Carbon::now()->subMonth()
                        )
                            ->whereNotNull("provider_id")
                            ->pluck("provider_id", "provider_id")
                            ->toArray();

                        $Providers = array_flip(
                            array_merge(
                                $StoreOrder,
                                $ServiceRequest,
                                $RideRequest
                            )
                        );
                    }
                } elseif ($push->condition == "RIDES") {
                    $StoreOrder = StoreOrder::select(
                        "id",
                        "provider_id",
                        \DB::raw("COUNT(id) as total_number")
                    )
                        ->whereNotNull("provider_id")
                        ->groupBy("provider_id")
                        ->get();
                    $ServiceRequest = ServiceRequest::select(
                        "id",
                        "provider_id",
                        \DB::raw("COUNT(id) as total_number")
                    )
                        ->whereNotNull("provider_id")
                        ->groupBy("provider_id")
                        ->get();

                    $RideRequest = RideRequest::select(
                        "id",
                        "provider_id",
                        \DB::raw("COUNT(id) as total_number")
                    )
                        ->whereNotNull("provider_id")
                        ->groupBy("provider_id")
                        ->get();
                    if (count($StoreOrder) > 0) {
                        $total_order = collect($StoreOrder)
                            ->merge($ServiceRequest ?: collect())
                            ->merge($RideRequest ?: collect());
                    } elseif (count($ServiceRequest) > 0) {
                        $total_order = collect($ServiceRequest)
                            ->merge($StoreOrder ?: collect())
                            ->merge($RideRequest ?: collect());
                    } elseif (count($RideRequest) > 0) {
                        $total_order = collect($RideRequest)
                            ->merge($ServiceRequest ?: collect())
                            ->merge($StoreOrder ?: collect());
                    }
                    $total_users = [];
                    if (count($total_order) > 0) {
                        foreach ($total_order as $key => $val) {
                            if (@$total_users[@$val->provider_id] == "") {
                                @$total_users[@$val->provider_id] = @$val->total_number;
                            } else {
                                @$total_users[@$val->provider_id] =
                                    @$total_users[@$val->provider_id] +
                                    @$val->total_number;
                            }
                        }
                        foreach ($total_users as $key => $val) {
                            if ($val >= $push->condition_data) {
                                $Providers[$key] = $val;
                            }
                        }
                    }
                } elseif ($push->condition == "LOCATION") {
                    $Location = explode(",", $push->condition_data);

                    $distance = config(
                        "constants.provider_search_radius",
                        "10"
                    );
                    $latitude = $Location[0];
                    $longitude = $Location[1];

                    $Providers = Provider::whereRaw(
                        "(1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance"
                    )
                        ->pluck("id", "id")
                        ->toArray();
                }

                foreach ($Providers as $key => $provider) {
                    (new SendPushNotification())->sendPushToProvider(
                        $key,
                        "",
                        $push->message
                    );
                }
            } elseif ($push->send_to == "ALL") {
                $users = User::all();
                foreach ($users as $key => $user) {
                    (new SendPushNotification())->sendPushToUser(
                        $user->id,
                        "",
                        $push->message
                    );
                }

                $Providers = Provider::all();
                foreach ($Providers as $key => $provider) {
                    (new SendPushNotification())->sendPushToProvider(
                        $provider->id,
                        "",
                        $push->message
                    );
                }
            }
        } catch (Exception $e) {
            return back()->with(
                "flash_error",
                "Something Went Wrong!" . $e->getMessage()
            );
        }
    }

    /**
     * Display the specified resource.
     *
     */
    public function show($id): JsonResponse
    {
        try {
            $custom_push = CustomPush::findOrFail($id);
            return Helper::getResponse(["data" => $custom_push]);
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
            "send_to" => "required|in:ALL,USERS,PROVIDERS",
            "user_condition" => [
                "required_if:send_to,USERS",
                "in:ACTIVE,LOCATION,RIDES,AMOUNT",
            ],
            "provider_condition" => [
                "required_if:send_to,PROVIDERS",
                "in:ACTIVE,LOCATION,RIDES,AMOUNT",
            ],
            "user_active" => [
                "required_if:user_condition,ACTIVE",
                "in:HOUR,WEEK,MONTH",
            ],
            "user_rides" => "required_if:user_condition,RIDES",
            "user_location" => "required_if:user_condition,LOCATION",
            "user_amount" => "required_if:user_condition,AMOUNT",
            "provider_active" => [
                "required_if:provider_condition,ACTIVE",
                "in:HOUR,WEEK,MONTH",
            ],
            "provider_rides" => "required_if:provider_condition,RIDES",
            "provider_location" => "required_if:provider_condition,LOCATION",
            "provider_amount" => "required_if:provider_condition,AMOUNT",
            "message" => "required|max:100",
        ]);
        try {
            $CustomPush = CustomPush::findOrFail($id);
            $CustomPush->send_to = $request->send_to;
            $CustomPush->message = $request->message;
            $CustomPush->company_id = Auth::user()->company_id;

            if ($request->send_to == "USERS") {
                $CustomPush->condition = $request->user_condition;

                if ($request->user_condition == "ACTIVE") {
                    $CustomPush->condition_data = $request->user_active;
                } elseif ($request->user_condition == "LOCATION") {
                    $CustomPush->condition_data = $request->user_location;
                } elseif ($request->user_condition == "RIDES") {
                    $CustomPush->condition_data = $request->user_rides;
                } elseif ($request->user_condition == "AMOUNT") {
                    $CustomPush->condition_data = $request->user_amount;
                }
            } elseif ($request->send_to == "PROVIDERS") {
                $CustomPush->condition = $request->provider_condition;

                if ($request->provider_condition == "ACTIVE") {
                    $CustomPush->condition_data = $request->provider_active;
                } elseif ($request->provider_condition == "LOCATION") {
                    $CustomPush->condition_data = $request->provider_location;
                } elseif ($request->provider_condition == "RIDES") {
                    $CustomPush->condition_data = $request->provider_rides;
                } elseif ($request->provider_condition == "AMOUNT") {
                    $CustomPush->condition_data = $request->provider_amount;
                }
            }

            if (
                $request->has("schedule_date") &&
                $request->has("schedule_time")
            ) {
                $CustomPush->schedule_at = date(
                    "Y-m-d H:i:s",
                    strtotime("$request->schedule_date $request->schedule_time")
                );
            }
            $CustomPush->save();
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

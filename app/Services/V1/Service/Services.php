<?php

namespace App\Services\V1\Service;

use Admin;
use App\Helpers\Helper;
use App\Models\Common\AdminService;
use App\Models\Common\Card;
use App\Models\Common\CompanyCountry;
use App\Models\Common\Provider;
use App\Models\Common\RequestFilter;
use App\Models\Common\State;
use App\Models\Common\User;
use App\Models\Common\UserRequest;
use App\Models\Service\Service;
use App\Models\Service\ServiceRequest;
use App\Services\SendPushNotification;
use App\Traits\Actions;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

class Services
{
    use Actions;

    public function create_service(Request $request)
    {
        $provider_id = $request->id;
        $provider = Provider::find($provider_id);

        $FilterCheck = RequestFilter::where([
            "admin_service" => "SERVICE",
            "provider_id" => $provider_id,
            "company_id" => $this->company_id,
        ])->first();

        if ($FilterCheck != null) {
            return [
                "status" => 422,
                "message" => trans("api.ride.request_inprogress"),
            ];
        }

        $ActiveRequests = ServiceRequest::PendingRequest(
            Auth::guard("user")->user()->id
        )->count();



        $timezone = State::find($this->user->state_id)->timezone;
        $currency = CompanyCountry::where("company_id", $this->company_id)
            ->where("country_id", $this->user->country_id)
            ->first();
        if ($request->has("schedule_date") && $request->has("schedule_time")) {
            $schedule_date = Carbon::createFromFormat(
                "Y-m-d H:i:s",
                Carbon::parse(
                    $request->schedule_date . " " . $request->schedule_time
                )->format("Y-m-d H:i:s"),
                $timezone
            )->setTimezone("UTC");

            $beforeschedule_time = (new Carbon($schedule_date))->subHour(1);
            $afterschedule_time = (new Carbon($schedule_date))->addHour(1);

            $CheckScheduling = ServiceRequest::where("status", "SCHEDULED")
                ->where("user_id", $this->user->id)
                ->whereBetween("schedule_at", [
                    $beforeschedule_time,
                    $afterschedule_time,
                ])
                ->count();

            if ($CheckScheduling > 0) {
                return [
                    "status" => 422,
                    "message" => trans("api.ride.request_already_scheduled"),
                ];
            }
        }
        $distance = $this->settings->service->provider_search_radius
            ? $this->settings->service->provider_search_radius
            : 100;

        $latitude = $request->s_latitude;
        $longitude = $request->s_longitude;
        $service_id = $request->service_id;

        $Provider = Provider::with("service", "rating")
            ->select(
                DB::Raw(
                    "(3959 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) AS distance"
                ),
                "id",
                "first_name",
                "picture"
            )
            ->where("id", $provider_id)
            ->orderBy("distance", "asc")
            ->first();

        try {
            $details =
                "https://maps.googleapis.com/maps/api/directions/json?origin=" .
                $request->s_latitude .
                "," .
                $request->s_longitude .
                "&mode=driving&key=" .
                $this->settings->site->browser_key;

            $json = Helper::curl($details);

            $details = json_decode($json, true);

            $route_key =
                !empty($details["routes"])
                ? $details["routes"][0]["overview_polyline"]["points"]
                : "";

            $serviceRequest = new ServiceRequest();
            $serviceRequest->company_id = $this->company_id;
            $prefix = $this->settings->service->booking_prefix;
            $serviceRequest->booking_id = Helper::generateBookingId($prefix);
            $serviceRequest->admin_service = "SERVICE";
            $serviceRequest->timezone = $timezone;
            $serviceRequest->user_id = $this->user->id;

            $serviceRequest->service_id = $request->service_id;

            $serviceRequest->service_category_id = Service::select(
                "service_category_id"
            )
                ->where("id", $request->service_id)
                ->first()->service_category_id;
            $serviceRequest->provider_id = $provider_id;
            $serviceRequest->payment_mode = $request->payment_mode;
            $serviceRequest->promocode_id = $request->promocode_id ?: 0;

            $serviceRequest->status = "SEARCHING";

            $serviceRequest->timezone = $timezone;
            $serviceRequest->currency =
                $currency != null ? $currency->currency : "";



            $serviceRequest->s_address = $request->s_address
                ? $request->s_address
                : "Address";

            $serviceRequest->s_latitude = $latitude;
            $serviceRequest->s_longitude = $longitude;

            $serviceRequest->track_latitude = $latitude;
            $serviceRequest->track_longitude = $longitude;

            $serviceRequest->allow_description = $request->allow_description;
            if (
                $request->hasFile("allow_image") &&
                $request->allow_image != ""
            ) {
                $serviceRequest->allow_image = Helper::upload_file(
                    $request->file("allow_image"),
                    "service/image",
                    null,
                    $this->company_id
                );
            }
            $serviceRequest->quantity = $request->quantity;
            $serviceRequest->price = $request->price;

            $serviceRequest->distance = $request->distance
                ? $request->distance
                : 0;
            $serviceRequest->unit = $this->settings->site->distance;

            if ($this->user->wallet_balance > 0) {
                $serviceRequest->use_wallet = $request->use_wallet ?: 0;
            }

            $serviceRequest->otp = mt_rand(1000, 9999);

            $serviceRequest->assigned_at = Carbon::now()->toDateTimeString();
            $serviceRequest->route_key = $route_key;

            if (
                $request->has("schedule_date") &&
                $request->has("schedule_time") &&
                $request->schedule_date != "" &&
                $request->schedule_time != ""
            ) {
                $serviceRequest->status = "SCHEDULED";
                $serviceRequest->schedule_at = Carbon::createFromFormat(
                    "Y-m-d H:i:s",
                    Carbon::parse(
                        $request->schedule_date . " " . $request->schedule_time
                    )->format("Y-m-d H:i:s"),
                    $timezone
                );
                //->setTimezone('UTC');
                $serviceRequest->is_scheduled = "YES";
            }

            $serviceRequest->save();

            // update payment mode
            User::where("id", $this->user->id)->update([
                "payment_mode" => $request->payment_mode,
            ]);

            if ($request->has("card_id")) {
                Card::where("user_id", $this->user->id)->update([
                    "is_default" => 0,
                ]);
                Card::where("card_id", $request->card_id)->update([
                    "is_default" => 1,
                ]);
            }

            $serviceRequest = ServiceRequest::with(
                "service",
                "service.serviceCategory",
                "service.servicesubCategory"
            )
                ->where("id", $serviceRequest->id)
                ->first();

            //Add the Log File for ride
            $serviceRequestId = $serviceRequest->id;
            $user_request = new UserRequest();
            $user_request->request_id = $serviceRequest->id;
            $user_request->user_id = $serviceRequest->user_id;
            $user_request->provider_id = $serviceRequest->provider_id;
            $user_request->schedule_at = $serviceRequest->schedule_at;
            $user_request->company_id = $this->company_id;
            $user_request->admin_service = "SERVICE";
            $user_request->status = $serviceRequest->status;
            $user_request->request_data = json_encode($serviceRequest);
            $user_request->save();

            Log::info($user_request);
            if ($serviceRequest->status != "SCHEDULED") {
                if ($this->settings->service->manual_request == 0) {
                    (new SendPushNotification())->IncomingRequest(
                        @$Provider->id,
                        "service"
                    );


                    $Filter = new RequestFilter();
                    // Send push notifications to the first provider
                    // incoming request push to provider
                    $Filter->admin_service = "SERVICE";
                    $Filter->request_id = $user_request->id;
                    $Filter->provider_id = $provider_id;

                    $Filter->company_id = $this->company_id;
                    $Filter->save();
                }
                //Send message to socket
                $requestData = [
                    "type" => "SERVICE",
                    "room" => "room_" . $this->company_id,
                    "id" => $serviceRequest->id,
                    "city" =>
                        $this->settings->demo_mode == 0
                        ? $serviceRequest->city_id
                        : 0,
                    "user" => $serviceRequest->user_id,
                ];
                app("redis")->publish("newRequest", json_encode($requestData));
            }

            return [
                "message" =>
                    $serviceRequest->status == "SCHEDULED"
                    ? "Schedule request created!"
                    : "New request created!",
                "data" => [
                    "message" =>
                        $serviceRequest->status == "SCHEDULED"
                        ? "Schedule request created!"
                        : "New request created!",
                    "request" => $serviceRequest->id,
                ],
            ];
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    public function cancelService(Request $request)
    {
        try {
            $serviceRequest = ServiceRequest::findOrFail($request->id);

            if ($serviceRequest->status == "CANCELLED") {
                return [
                    "status" => 404,
                    "message" => trans("api.service.already_cancelled"),
                ];
            }

            if (
                $serviceRequest->status == "PICKEDUP" ||
                $serviceRequest->status == "DROPPED" ||
                $serviceRequest->status == "COMPLETED"
            ) {
                return [
                    "status" => 404,
                    "message" => trans("api.service.request_inprogress"),
                ];
            } else {
                if ($serviceRequest->status != "SEARCHING") {
                    $validator = Validator::make($request->all(), [
                        "cancel_reason" => "max:255",
                    ]);

                    if ($validator->fails()) {
                        $errors = [];
                        foreach (json_decode($validator->errors(), true) as $key => $error) {
                            $errors[] = $error[0];
                        }

                        header("Access-Control-Allow-Origin: *");
                        header("Access-Control-Allow-Headers: *");
                        header("Content-Type: application/json");
                        http_response_code(422);
                        echo json_encode(
                            Helper::getResponse([
                                "status" => 422,
                                "message" => !empty($errors[0])
                                    ? $errors[0]
                                    : "",
                                "error" => !empty($errors[0]) ? $errors[0] : "",
                            ])->original
                        );
                        exit();
                    }
                }
                $serviceRequest->status = "CANCELLED";
                if ($request->cancel_reason == "ot") {
                    $serviceRequest->cancel_reason =
                        $request->cancel_reason_opt;
                } else {
                    $serviceRequest->cancel_reason = $request->cancel_reason;
                }
                $serviceRequest->cancelled_by = $request->cancelled_by;
                $serviceRequest->save();

                $admin_service = AdminService::where("admin_service", "SERVICE")
                    ->where(
                        "company_id",
                        Auth::guard("user")->user()->company_id
                    )
                    ->first();
                $user_request = UserRequest::where("admin_service", "SERVICE")
                    ->where("request_id", $serviceRequest->id)
                    ->first();
                if ($user_request != null) {
                    $requestFilter = RequestFilter::where(
                        "admin_service",
                        "SERVICE"
                    )
                        ->where("request_id", $user_request->id)
                        ->first();
                    $user_request->delete();
                    if ($requestFilter != null) {
                        $requestFilter->delete();
                    }
                }
                if ($serviceRequest->status != "SCHEDULED" && $serviceRequest->provider_id != null) {

                    Provider::where(
                        "id",
                        $serviceRequest->provider_id
                    )->update(["status" => "approved", "is_assigned" => 0]);
                }
                // Send Push Notification to User
                //(new SendPushNotification)->UserCancellRide($rideRequest, 'service');
                //Send message to socket
                $requestData = [
                    "type" => "SERVICE",
                    "room" => "room_" . Auth::guard("user")->user()->company_id,
                    "user" => $serviceRequest->user_id,
                ];
                app("redis")->publish(
                    "checkServiceRequest",
                    json_encode($requestData)
                );
                return ["message" => trans("api.service.ride_cancelled")];
            }
        } catch (ModelNotFoundException $e) {
            return ["status" => 500, "error" => $e->getMessage()];
        }
    }
}

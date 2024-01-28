<?php

namespace App\Console\Commands;

use App\Models\Common\Provider;
use App\Models\Common\RequestFilter;
use App\Models\Common\Setting;
use App\Models\Common\UserRequest;
use App\Models\Service\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ServiceScheduleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronjob:services';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updating the Scheduled Services Timing';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Log::info("cron runing in service");
        $userRequest = UserRequest::where('status', 'SCHEDULED')->where('status', '!=', 'INPROGRESS')
            ->where('admin_service', 'SERVICE')
            ->where('schedule_at', '<=', Carbon::now()->addMinutes(20))
            ->get();

        $hour = Carbon::now()->subHour();
        $futureHours = Carbon::now()->addMinutes(20);
        $date = Carbon::now();
        Log::info("Carbon now time " . $date);
        if (!empty($userRequest)) {

            foreach ($userRequest as $newRequest) {
                $serviceRequest = ServiceRequest::find($newRequest->request_id);

                $service_id = $serviceRequest->service_id != '' ? $serviceRequest->service_id : '';
                $ProviderCheck = Provider::where('is_online', 1)->where('id', $serviceRequest->provider_id)->where('is_assigned', 0)->where('status', 'APPROVED')
                    ->whereHas('service', function ($query) use ($service_id) {
                        $query->where('service_id', $service_id);
                        $query->where('admin_service', 'SERVICE');
                    })->first();

                if (!empty($ProviderCheck)) {
                    $Filter = new RequestFilter;
                    // Send push notifications to the first provider
                    $Filter->admin_service = 'SERVICE';
                    $Filter->request_id = $newRequest->id;
                    $Filter->provider_id = $serviceRequest->provider_id;
                    $Filter->company_id = $serviceRequest->company_id;
                    $Filter->save();
                    Log::info("Schedule Service Request Started." . $date . "==" . $hour . "==" . $futureHours);
                    $setting = Setting::where('company_id', $serviceRequest->company_id)->first();

                    $serviceRequest->status = "SEARCHING";
                    $serviceRequest->assigned_at = Carbon::now();
                    $serviceRequest->schedule_at = null;
                    $serviceRequest->save();

                    $serviceData = ServiceRequest::with('service', 'service.serviceCategory', 'service.servicesubCategory')->where('id', $serviceRequest->id)->first();

                    $newRequest->status = $serviceRequest->status;
                    $newRequest->request_data = json_encode($serviceData);
                    $newRequest->save();

                    //Send message to socket
                    $requestData = ['type' => 'SERVICE', 'room' => 'room_' . $serviceRequest->company_id, 'id' => $serviceRequest->id, 'city' => ($setting->demo_mode == 0) ? $serviceRequest->city_id : 0, 'user' => $serviceRequest->user_id];
                    app('redis')->publish('newRequest', json_encode($requestData));
                } else {

                    $setting = Setting::where('company_id', $serviceRequest->company_id)->first();

                    $settings = json_decode(json_encode($setting->settings_data));

                    $siteConfig = $settings->site;

                    $serviceConfig = $settings->service;

                    $distance = isset($serviceConfig->provider_search_radius) ? $serviceConfig->provider_search_radius : 10;
                    $latitude = $serviceRequest->s_latitude;
                    $longitude = $serviceRequest->s_longitude;
                    $service_id = $serviceRequest->service_id;

                    $Providers = Provider::select(DB::Raw("(3959 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) AS distance"), 'id', 'first_name', 'picture', 'rating', 'city_id')
                        ->where('status', 'approved')
                        ->where('is_online', 1)
                        ->where('is_assigned', 0)
                        ->whereRaw("(3959 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance")
                        ->whereDoesntHave('request_filter')
                        ->whereHas('service', function ($query) use ($service_id) {
                            $query->where('service_id', $service_id);
                            $query->where('admin_service', 'SERVICE');
                        })
                        ->orderBy('distance', 'asc')->get();

                    if (!empty($Providers->toArray())) {
                        foreach ($Providers as $Provider) {
                            $Filter = new RequestFilter();
                            // Send push notifications to the first provider
                            $Filter->admin_service = 'SERVICE';
                            $Filter->request_id = $newRequest->id;
                            $Filter->provider_id = $Provider->id;
                            $Filter->company_id = $newRequest->company_id;
                            $Filter->save();
                        }
                        Log::info("Schedule Service Request Started2." . $date . "==" . $hour . "==" . $futureHours);

                        $serviceRequest->status = "SEARCHING";
                        $serviceRequest->assigned_at = Carbon::now();
                        $serviceRequest->schedule_at = null;
                        $serviceRequest->save();

                        $serviceData = ServiceRequest::with('service', 'service.serviceCategory', 'service.servicesubCategory')->where('id', $serviceRequest->id)->first();

                        $newRequest->status = $serviceRequest->status;
                        $newRequest->request_data = json_encode($serviceData);
                        $newRequest->save();

                        //Send message to socket
                        $requestData = ['type' => 'SERVICE', 'room' => 'room_' . $serviceRequest->company_id, 'id' => $serviceRequest->id, 'city' => ($setting->demo_mode == 0) ? $serviceRequest->city_id : 0, 'user' => $serviceRequest->user_id];
                        app('redis')->publish('newRequest', json_encode($requestData));
                    } else {

                        $serviceRequest->status = "CANCELLED";
                        $serviceRequest->assigned_at = Carbon::now();
                        $serviceRequest->schedule_at = null;
                        $serviceRequest->cancel_reason = 'Scheduled provider not found';
                        $serviceRequest->save();

                        $user_request = UserRequest::where('admin_service', 'SERVICE')->where('request_id', $serviceRequest->id)->first();

                        $user_request->delete();
                    }
                }
            }
        }
    }
}

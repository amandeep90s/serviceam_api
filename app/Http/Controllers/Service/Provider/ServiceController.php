<?php

namespace App\Http\Controllers\Service\Provider;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\Admin;
use App\Models\Common\AdminService;
use App\Models\Common\Dispute;
use App\Models\Common\Promocode;
use App\Models\Common\PromocodeUsage;
use App\Models\Common\Provider;
use App\Models\Common\ProviderService;
use App\Models\Common\Rating;
use App\Models\Common\Reason;
use App\Models\Common\RequestFilter;
use App\Models\Common\Setting;
use App\Models\Common\User;
use App\Models\Common\UserRequest;
use App\Models\Service\Service;
use App\Models\Service\ServiceCategory;
use App\Models\Service\ServiceRequest;
use App\Models\Service\ServiceRequestDispute;
use App\Models\Service\ServiceRequestPayment;
use App\Models\Service\ServiceSubCategory;
use App\Services\ReferralResource;
use App\Services\SendPushNotification;
use App\Services\Transactions;
use App\Services\V1\Common\ProviderServices;
use App\Traits\Actions;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ServiceController extends Controller
{
    use Actions;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $settings = json_decode(
                json_encode(
                    Setting::where(
                        "company_id",
                        Auth::guard("provider")->user()->company_id
                    )->first()->settings_data
                )
            );

            $siteConfig = $settings->site;
            $serviceConfig = $settings->service;

            if (!empty($request->latitude)) {
                Provider::where(
                    "id",
                    Auth::guard("provider")->user()->id
                )->update([
                    "latitude" => $request->latitude,
                    "longitude" => $request->longitude,
                ]);
                //when the provider is idle for a long time in the mobile app, it will change its status to hold. If it is waked up while new incoming request, here the status will change to active
                //DB::table('provider_services')->where('provider_id',$Provider->id)->where('status','hold')->update(['status' =>'active']);
            }
            $provider = Provider::with([
                "service" => function ($query) {
                    $query->where("admin_service", "SERVICE");
                },
            ])
                ->where("id", Auth::guard("provider")->user()->id)
                ->first();

            $provider = $provider->id;

            $IncomingRequests = ServiceRequest::with([
                "user",
                "payment",
                "service",
                "chat",
            ])
                ->where("provider_id", $provider)
                ->where("status", "<>", "CANCELLED")
                ->where("status", "<>", "SCHEDULED")
                ->where("provider_rated", "0")
                ->where("provider_id", $provider)
                ->first();

            $Reason = Reason::where("type", "PROVIDER")->get();

            $referral_total_count = (new ReferralResource())->get_referral(
                "provider",
                Auth::guard("provider")->user()->id
            )[0]->total_count;
            $referral_total_amount = (new ReferralResource())->get_referral(
                "provider",
                Auth::guard("provider")->user()->id
            )[0]->total_amount;

            if ($IncomingRequests != null) {
                $categoryId = $IncomingRequests->service->service_category_id;
                $subCategoryId =
                    $IncomingRequests->service->service_subcategory_id;
                $IncomingRequests->category = ServiceCategory::where(
                    "id",
                    $categoryId
                )->first();
                $IncomingRequests->subcategory = ServiceSubCategory::where(
                    "id",
                    $subCategoryId
                )->first();
                if ($IncomingRequests != null) {
                    if ($IncomingRequests->payment != null) {
                        $IncomingRequests->promo_code = Promocode::where(
                            "id",
                            $IncomingRequests->payment->promocode_id
                        )->first();
                    } else {
                        $IncomingRequests->promo_code = null;
                    }
                }
                $Provider_service = ProviderService::where(
                    "provider_id",
                    $IncomingRequests->provider_id
                )
                    ->where("service_id", $IncomingRequests->service_id)
                    ->first();
            }

            $Response = [
                "account_status" => $provider->status,
                "service_status" => !empty($IncomingRequests)
                    ? "SERVICE"
                    : "ACTIVE",
                "FareType" => isset($Provider_service->fare_type)
                    ? $Provider_service->fare_type
                    : "FIXED",
                "requests" => $IncomingRequests,
                "provider_details" => $provider,
                "reasons" => $Reason,
                "referral_count" => $siteConfig->referral_count,
                "referral_amount" => $siteConfig->referral_amount,
                "serve_otp" => 0,
                "referral_total_count" => $referral_total_count,
                "referral_total_amount" => $referral_total_amount,
            ];

            return Helper::getResponse(["data" => $Response]);
        } catch (ModelNotFoundException $e) {
            return Helper::getResponse([
                "status" => 500,
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function updateServe(Request $request)
    {
        $this->validate($request, [
            "status" =>
            "required|in:ACCEPTED,STARTED,ARRIVED,PICKEDUP,DROPPED,PAYMENT,COMPLETED",
        ]);
        try {
            $setting = Setting::where(
                "company_id",
                Auth::guard("provider")->user()->company_id
            )->first();
            $settings = json_decode(json_encode($setting->settings_data));

            $siteConfig = $settings->site;
            $serviceConfig = $settings->service;
            $serveRequest = ServiceRequest::with(
                "user",
                "service",
                "service.serviceCategory"
            )->findOrFail($request->id);

            $user_request = UserRequest::where("request_id", $request->id)
                ->where("admin_service", "SERVICE")
                ->first();
            if (
                $request->status == "PAYMENT" &&
                $serveRequest->payment_mode != "CASH"
            ) {
                $serveRequest->status = "COMPLETED";
                $serveRequest->paid = 0;

                (new SendPushNotification())->serviceProviderComplete(
                    $serveRequest,
                    "service",
                    "Service Completed"
                );
            } elseif (
                $request->status == "PAYMENT" &&
                $serveRequest->payment_mode == "CASH"
            ) {
                if ($serveRequest->status == "COMPLETED") {
                    //for off cross clicking on change payment issue on mobile
                    return Helper::getResponse(["data" => $serveRequest]);
                }
                $serveRequest->status = "COMPLETED";
                $serveRequest->paid = 1;
                (new SendPushNotification())->serviceProviderComplete(
                    $serveRequest,
                    "service",
                    "Service Completed"
                );
                //for completed payments
                $RequestPayment = ServiceRequestPayment::where(
                    "service_request_id",
                    $request->id
                )->first();
                $RequestPayment->payment_mode = "CASH";
                $RequestPayment->cash = $RequestPayment->payable;
                $RequestPayment->payable = 0;
                $RequestPayment->save();
            } else {
                $serveRequest->status = $request->status;
                if ($request->status == "ARRIVED") {
                    (new SendPushNotification())->serviceProviderArrived(
                        $serveRequest,
                        "service",
                        "Service Arrived"
                    );
                }
            }

            if ($request->status == "PICKEDUP") {
                $serveRequest->started_at = Carbon::now()->toDateTimeString();
                if (
                    isset($request->otp) &&
                    (isset($serviceConfig->serve_otp) &&
                        $serviceConfig->serve_otp == 1)
                ) {
                    if ($request->otp == $serveRequest->otp) {
                        if ($request->hasFile("before_picture")) {
                            $serveRequest->before_image = Helper::uploadProviderFile(
                                $request->file("before_picture"),
                                "xuber/requests",
                                "srbi-before-" . time() . ".png"
                            );
                        }
                        if ($serveRequest->is_track == "YES") {
                            $serveRequest->distance = 0;
                        }
                        (new SendPushNotification())->serviceProviderPickedup(
                            $serveRequest,
                            "service",
                            "Service Started"
                        );
                    } else {
                        return Helper::getResponse([
                            "status" => 500,
                            "message" => trans("api.otp"),
                            "error" => trans("api.otp"),
                        ]);
                    }
                } else {
                    if ($request->hasFile("before_picture")) {
                        $serveRequest->before_image = Helper::uploadProviderFile(
                            $request->file("before_picture"),
                            "xuber/requests",
                            "srbi-before-" . time() . ".png"
                        );
                    }
                    if ($serveRequest->is_track == "YES") {
                        $serveRequest->distance = 0;
                    }
                    (new SendPushNotification())->serviceProviderPickedup(
                        $serveRequest,
                        "service",
                        "Service Started"
                    );
                }
            }

            if ($request->status == "DROPPED") {
                $extracharges =
                    isset($request->extra_charge) &&
                    $request->extra_charge != ""
                    ? $request->extra_charge
                    : 0;
                $extracharges_notes =
                    isset($request->extra_charge_notes) &&
                    $request->extra_charge_notes != ""
                    ? $request->extra_charge_notes
                    : 0;
                $serveRequest->finished_at = Carbon::now()->toDateTimeString();
                $StartedDate = date_create($serveRequest->started_at);
                $FinisedDate = Carbon::now();
                $TimeInterval = date_diff($StartedDate, $FinisedDate);
                $MintuesTime = $TimeInterval->i;
                $serveRequest->travel_time = $MintuesTime;

                if ($request->hasFile("after_picture")) {
                    $serveRequest->after_image = Helper::uploadProviderFile(
                        $request->file("after_picture"),
                        "xuber/requests",
                        "srbi-after-" . time() . ".png"
                    );
                }
                $distance = isset($request->distance) ? $request->distance : 0;
                $serveRequest->save();
                $serveRequest->with("user")->findOrFail($request->id);
                $getInvoice = $this->invoice(
                    "SERVICE",
                    $request->id,
                    $extracharges,
                    $extracharges_notes,
                    $distance
                );

                (new SendPushNotification())->serviceProviderDropped(
                    $serveRequest,
                    "service",
                    "Service Dropped"
                );
            }
            if ($request->status == "PAYMENT") {
                $serveRequest->save();
                $serveRequest->with("user")->findOrFail($request->id);

                (new SendPushNotification())->serviceProviderConfirmPay(
                    $serveRequest,
                    "service",
                    "Confirm Payment"
                );
            }
            $serveRequest->save();
            if ($user_request != null) {
                $user_request->provider_id = $serveRequest->provider_id;
                $user_request->status = $serveRequest->status;
                $user_request->request_data = json_encode($serveRequest);

                $user_request->save();
            }
            //for completed payments
            $serveRequestResponse = ServiceRequest::with(
                "user",
                "payment",
                "service"
            )->findOrFail($serveRequest->id);
            if ($serveRequestResponse != null) {
                if ($serveRequestResponse->payment != null) {
                    $serveRequestResponse->promo_code = Promocode::where(
                        "id",
                        $serveRequestResponse->payment->promocode_id
                    )->first();
                } else {
                    $serveRequestResponse->promo_code = null;
                }
            }
            //Send message to socket
            $requestData = [
                "type" => "SERVICE",
                "room" => "room_" . Auth::guard("provider")->user()->company_id,
                "id" => $serveRequest->id,
                "city" => $setting->demo_mode == 0 ? $serveRequest->city_id : 0,
                "user" => $serveRequest->user_id,
            ];

            app("redis")->publish(
                "checkServiceRequest",
                json_encode($requestData)
            );

            //for create the transaction
            $this->callTransaction($request->id);

            return Helper::getResponse(["data" => $serveRequestResponse]);
        } catch (ModelNotFoundException $e) {
            return Helper::getResponse([
                "status" => 500,
                "message" => trans("api.unable_accept"),
                "error" => $e->getMessage(),
            ]);
        } catch (Exception $e) {
            return Helper::getResponse([
                "status" => 500,
                "message" => trans("api.connection_err"),
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Invoice method
     */
    public function invoice(
        $admin_service,
        $request_id,
        $extracharges,
        $extracharges_notes,
        $userdistance = ""
    ) {
        try {
            $UserRequest = ServiceRequest::findOrFail($request_id);
            $cityId = $UserRequest->city_id;
            $serviceId = $UserRequest->service_id;
            $companyId = $UserRequest->company_id;
            $providerId = $UserRequest->provider_id;
            $distance =
                $userdistance != 0 ? $userdistance : $UserRequest->distance;
            $serviceDetails = Service::with("serviceCategory")
                ->where("id", $serviceId)
                ->where("company_id", $companyId)
                ->first();
            //$cityPriceList = ServiceCityPrice::where(['service_id'=>$serviceId, 'city_id'=> $cityId])->first();
            $baseFare = 0;
            $perMiles = 0;
            $perMins = 0;

            $provider_service = ProviderService::where([
                "provider_id" => $providerId,
                "admin_service" => $admin_service,
                "service_id" => $serviceId,
            ])->first();

            if ($provider_service != null) {
                $fareType = $provider_service->fare_type;
                $getbaseFare = $provider_service->base_fare;
                $getperMins = $provider_service->per_mins;
                //$baseDistance = $cityPriceList->base_distance;
                $getperMiles = $provider_service->per_miles;
                //$commissionPercent = $cityPriceList->commission;
                //$taxPercent = $cityPriceList->tax;
                //$fleetPercent = $cityPriceList->fleet_commission;
                $baseDistance = 0;
                $commissionPercent = 0;
                $taxPercent = 0;
                $fleetPercent = 0;
            } else {
                $fareType = "FIXED";
                $getbaseFare = 0;
                $getperMins = 0;
                $baseDistance = 0;
                $getperMiles = 0;
                $commissionPercent = 0;
                $taxPercent = 0;
                $fleetPercent = 0;
                $price_choose = "";
            }

            if (
                $serviceDetails->serviceCategory->price_choose == "admin_price"
            ) {
                if (!empty($UserRequest->quantity)) {
                    $baseFare = Helper::decimalRoundOff(
                        $getbaseFare * $UserRequest->quantity
                    );
                } else {
                    $baseFare = Helper::decimalRoundOff($getbaseFare);
                }

                $perMiles = Helper::decimalRoundOff($getperMiles);
                $perMins = round($getperMins, 2);
            } else {
                if ($provider_service != null) {
                    if (!empty($UserRequest->quantity)) {
                        $baseFare = Helper::decimalRoundOff(
                            $provider_service->base_fare *
                                $UserRequest->quantity
                        );
                    } else {
                        $baseFare = Helper::decimalRoundOff(
                            $provider_service->base_fare
                        );
                    }

                    $perMiles = Helper::decimalRoundOff(
                        $provider_service->per_miles
                    );
                    $perMins = Helper::decimalRoundOff(
                        $provider_service->per_mins
                    );
                }
            }

            $price_choose = $serviceDetails->serviceCategory->price_choose;

            $to = \Carbon\Carbon::createFromFormat(
                "Y-m-d H:i:s",
                $UserRequest->finished_at
            );
            $from = \Carbon\Carbon::createFromFormat(
                "Y-m-d H:i:s",
                $UserRequest->started_at
            );
            $diff_in_minutes = $to->diffInMinutes($from);
            if ($fareType == "HOURLY") {
                $fareAmount = $baseFare + $perMins * $diff_in_minutes;
            } elseif ($fareType == "DISTANCETIME") {
                $minsAmount = $perMins * $diff_in_minutes;
                if ($baseDistance >= $distance) {
                    $fareAmount = $baseFare + $minsAmount;
                } else {
                    $distanceAmount = abs($distance - $baseDistance);
                    $fareAmount = $baseFare + $distanceAmount + $minsAmount;
                }
            } else {
                // FIXED PRICE TYPE
                $fareAmount = $baseFare;
            }
            $promoId =
                $UserRequest->promocode_id != 0
                ? $UserRequest->promocode_id
                : 0;
            $Discount = 0;
            $discount_per = 0;
            $Wallet = 0;
            $commissionAmount = $fareAmount * ($commissionPercent / 100);
            $Fixed = $fareAmount + $commissionAmount;

            $taxAmount = $Fixed * ($taxPercent / 100);
            $Total = $Fixed + $extracharges + $taxAmount;

            if ($promoId > 0) {
                if ($Promocode = Promocode::find($UserRequest->promocode_id)) {
                    $max_amount = $Promocode->max_amount;
                    $discount_per = $Promocode->percentage;
                    $discount_amount = $Total * ($discount_per / 100);
                    if ($discount_amount > $Promocode->max_amount) {
                        $Discount = $Promocode->max_amount;
                    } else {
                        $Discount = $discount_amount;
                    }

                    $PromocodeUsage = new PromocodeUsage();
                    $PromocodeUsage->user_id = $UserRequest->user_id;
                    $PromocodeUsage->company_id = Auth::guard(
                        "provider"
                    )->user()->company_id;
                    $PromocodeUsage->promocode_id = $UserRequest->promocode_id;
                    $PromocodeUsage->status = "USED";
                    $PromocodeUsage->save();
                }
            }
            $Payamount = $Total - $Discount;
            $ProviderPay = $Total + $Discount - $commissionAmount - $taxAmount;

            $Payment = new ServiceRequestPayment();
            if (!empty($UserRequest->admin_id)) {
                $Fleet = Admin::where("id", $UserRequest->admin_id)
                    ->where("type", "FLEET")
                    ->where(
                        "company_id",
                        Auth::guard("provider")->user()->company_id
                    )
                    ->first();

                $fleet_per = 0;

                if (!empty($Fleet)) {
                    if (!empty($commissionAmount)) {
                        $fleet_per = $Fleet->commision ? $Fleet->commision : 0;
                    } else {
                        //$fleet_per=$cityPriceList->fleet_commission ? $cityPriceList->fleet_commission :0;
                        $fleet_per = 0;
                    }

                    $Payment->fleet_id = $UserRequest->admin_id;
                    $Payment->fleet_percent = $fleet_per;
                }
            }

            // $Total += $Tax;

            if ($Total < 0) {
                $Total = 0.0; // prevent from negative value
            }
            $currencySymbol = $UserRequest->currency;
            $Payment->user_id = $UserRequest->user_id;
            $Payment->provider_id = $UserRequest->provider_id;
            $Payment->service_request_id = $UserRequest->id;
            $Payment->company_id = $UserRequest->company_id;
            $Payment->payment_mode = $UserRequest->payment_mode;
            $Payment->fixed = $baseFare;
            $Payment->provider_pay = $ProviderPay;
            $Payment->minute = $fareAmount - $baseFare;
            $Payment->commision = $commissionAmount;
            $Payment->commision_percent = $commissionPercent;
            $Payment->tax = $taxAmount;
            $Payment->tax_percent = $taxPercent;
            $Payment->total = $Total;
            $Payment->extra_charges = $extracharges;
            $Payment->extra_charges_notes = $extracharges_notes;
            if ($promoId != 0) {
                $Payment->promocode_id = $promoId;
            }
            if ($Discount != 0 && $PromocodeUsage) {
                $Payment->promocode_id = $PromocodeUsage->promocode_id;
            }
            $Payment->discount = $Discount;
            $Payment->discount_percent = $discount_per;
            if ($UserRequest->use_wallet == 1 && $Total > 0) {
                $User = User::find($UserRequest->user_id);
                $Wallet = $User->wallet_balance;
                if ($Wallet != 0 && $Wallet != 0.0) {
                    if ($Payamount > $Wallet) {
                        $Payment->wallet = $Wallet;
                        $Payable = $Payamount - $Wallet;
                        $Payment->total = abs($Total);
                        $Payment->payable = abs($Payable);
                        $Payment->is_partial = 1;

                        if ($UserRequest->payment_mode == "CASH") {
                            $Payment->round_of =
                                round($Payable) - abs($Payable);
                            $Payment->total = abs($Total);
                            $Payment->payable = round($Payable);
                        }

                        // charged wallet money push
                        // (new SendPushNotification)->ChargedWalletMoney($UserRequest->user_id,$Wallet);
                        (new SendPushNotification())->ChargedWalletMoney(
                            $UserRequest->user_id,
                            Helper::currencyFormat($Wallet, $currencySymbol),
                            "service",
                            "Wallet Info"
                        );

                        $transaction["amount"] = $Wallet;
                        $transaction["id"] = $UserRequest->user_id;
                        $transaction["transaction_id"] = $UserRequest->id;
                        $transaction["transaction_alias"] =
                            $UserRequest->booking_id;
                        $transaction["company_id"] = $UserRequest->company_id;
                        $transaction["transaction_msg"] = "service deduction";

                        (new Transactions())->userCreditDebit($transaction, 0);
                    } else {
                        $WalletBalance = $Wallet - $Total;
                        $Payment->wallet = $Payamount;
                        $Payment->payable = abs($Payamount);
                        //update user request table
                        $UserRequest->paid = 1;
                        $UserRequest->status = "COMPLETED";
                        $UserRequest->save();
                        // charged wallet money push
                        // (new SendPushNotification)->ChargedWalletMoney($UserRequest->user_id,$Total);
                        (new SendPushNotification())->ChargedWalletMoney(
                            $UserRequest->user_id,
                            Helper::currencyFormat($Payamount, $currencySymbol),
                            "service",
                            "Wallet Info"
                        );

                        $transaction["amount"] = $Payamount;
                        $transaction["id"] = $UserRequest->user_id;
                        $transaction["transaction_id"] = $UserRequest->id;
                        $transaction["transaction_alias"] =
                            $UserRequest->booking_id;
                        $transaction["company_id"] = $UserRequest->company_id;
                        $transaction["transaction_msg"] = "service deduction";

                        (new Transactions())->userCreditDebit($transaction, 0);
                    }
                }
            } else {
                if ($UserRequest->payment_mode == "CASH") {
                    $Payment->round_of = round($Payamount) - abs($Payamount);
                    $Payment->total = abs($Total);
                    $Payment->payable = round($Payamount);
                } else {
                    $Payment->total = abs($Total);
                    $Payment->payable = abs($Payamount);
                }
            }

            $Payment->tax = $taxAmount;
            $Payment->save();

            return $Payment;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    /**
     * Call transaction
     */
    public function callTransaction($request_id)
    {
        $UserRequest = ServiceRequest::with("provider")
            ->with("payment")
            ->findOrFail($request_id);

        if ($UserRequest->paid == 1) {
            $transation = [];
            $transation["admin_service"] = "SERVICE";
            $transation["company_id"] = $UserRequest->company_id;
            $transation["transaction_id"] = $UserRequest->id;
            $transation["country_id"] = $UserRequest->country_id;
            $transation["transaction_alias"] = $UserRequest->booking_id;

            $paymentsRequest = ServiceRequestPayment::where(
                "service_request_id",
                $request_id
            )->first();

            $provider = Provider::where(
                "id",
                $paymentsRequest->provider_id
            )->first();

            $fleet_amount = $discount = $admin_commision = $credit_amount = $balance_provider_credit = $provider_credit = 0;

            if ($paymentsRequest->is_partial == 1) {
                //partial payment
                if ($paymentsRequest->payment_mode == "CASH") {
                    $credit_amount =
                        $paymentsRequest->wallet + $paymentsRequest->tips;
                } else {
                    $credit_amount =
                        $paymentsRequest->total + $paymentsRequest->tips;
                }
            } else {
                if (
                    $paymentsRequest->payment_mode == "CARD" ||
                    $paymentsRequest->payment_id == "WALLET"
                ) {
                    $credit_amount =
                        $paymentsRequest->total + $paymentsRequest->tips;
                } else {
                    $credit_amount = 0;
                }
            }

            //admin,fleet,provider calculations
            if (!empty($paymentsRequest->commision)) {
                $admin_commision = $paymentsRequest->commision;

                if (!empty($paymentsRequest->fleet_id)) {
                    //get the percentage of fleet owners
                    $fleet_per = $paymentsRequest->fleet_percent;
                    $fleet_amount = $admin_commision * ($fleet_per / 100);
                    $admin_commision = $admin_commision;
                }

                //check the user applied discount
                if (!empty($paymentsRequest->discount)) {
                    $balance_provider_credit = $paymentsRequest->discount;
                }
            } else {
                if (!empty($paymentsRequest->fleet_id)) {
                    $fleet_per = $paymentsRequest->fleet_percent;
                    $fleet_amount =
                        $paymentsRequest->total * ($fleet_per / 100);
                    $admin_commision = $fleet_amount;
                }
                if (!empty($paymentsRequest->discount)) {
                    $balance_provider_credit = $paymentsRequest->discount;
                }
            }

            if (!empty($admin_commision)) {
                //add the commission amount to admin wallet and debit amount to provider wallet, update the provider wallet amount to provider table
                $transation["id"] = $paymentsRequest->provider_id;
                $transation["amount"] = $admin_commision;
                (new Transactions())->adminCommission($transation);
            }

            if (!empty($paymentsRequest->fleet_id) && !empty($fleet_amount)) {
                $paymentsRequest->fleet = $fleet_amount;
                $paymentsRequest->save();
                //create the amount to fleet account and deduct the amount to admin wallet, update the fleet wallet amount to fleet table
                $transation["id"] = $paymentsRequest->fleet_id;
                $transation["amount"] = $fleet_amount;
                (new Transactions())->fleetCommission($transation);
            }
            if (!empty($balance_provider_credit)) {
                //debit the amount to admin wallet and add the amount to provider wallet, update the provider wallet amount to provider table
                $transation["id"] = $paymentsRequest->provider_id;
                $transation["amount"] = $balance_provider_credit;
                (new Transactions())->providerDiscountCredit($transation);
            }

            if (!empty($paymentsRequest->tax)) {
                //debit the amount to provider wallet and add the amount to admin wallet
                $transation["id"] = $paymentsRequest->provider_id;
                $transation["amount"] = $paymentsRequest->tax;
                (new Transactions())->taxCredit($transation);
            }

            if ($credit_amount > 0) {
                //provider ride amount
                //check whether provider have any negative wallet balance if its deduct the amount from its credit.
                //if its negative wallet balance grater of its credit amount then deduct credit-wallet balance and update the negative amount to admin wallet
                $transation["id"] = $paymentsRequest->provider_id;
                $transation["amount"] = $credit_amount;

                if ($provider->wallet_balance > 0) {
                    $transation["admin_amount"] =
                        $credit_amount -
                        ($admin_commision + $paymentsRequest->tax);
                } else {
                    $transation["admin_amount"] =
                        $credit_amount -
                        ($admin_commision + $paymentsRequest->tax) +
                        $provider->wallet_balance;
                }

                (new Transactions())->providerRideCredit($transation);
            }

            return true;
        } else {
            return true;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function cancelServe(Request $request)
    {
        $setting = Setting::where(
            "company_id",
            Auth::guard("provider")->user()->company_id
        )->first();
        $settings = json_decode(json_encode($setting->settings_data));

        $siteConfig = $settings->site;
        $transportConfig = $settings->service;
        $serviceRequest = ServiceRequest::findOrFail($request->id);

        $user_request = UserRequest::where("request_id", $request->id)
            ->where("admin_service", "SERVICE")
            ->first();

        $admin_service = AdminService::where("admin_service", "SERVICE")
            ->where("company_id", Auth::guard("provider")->user()->company_id)
            ->first();
        $serviceDelete = RequestFilter::where("admin_service", "SERVICE")
            ->where("request_id", $user_request->id)
            ->first();
        if (!empty($user_request)) {
            if ($serviceDelete != null) {
                $serviceDelete->delete();
                $user_request->delete();
            }
            if ($serviceRequest != null) {
                $cancelreason = isset($request->reason)
                    ? $request->reason
                    : "cancelled";
                ServiceRequest::where("id", $serviceRequest->id)->update([
                    "status" => "CANCELLED",
                    "cancelled_by" => "PROVIDER",
                    "cancel_reason" => $cancelreason,
                ]);
                //ProviderService::where('provider_id',$serviceRequest->provider_id)->update(['status' => 'active']);
                Provider::where("id", $serviceRequest->provider_id)->update([
                    "is_assigned" => 0,
                ]);
            }
        }
        //Send message to socket
        $requestData = [
            "type" => "SERVICE",
            "room" => "room_" . Auth::guard("provider")->user()->company_id,
            "id" => $serviceRequest->id,
            "user" => $serviceRequest->user_id,
        ];
        app("redis")->publish("checkServiceRequest", json_encode($requestData));

        return Helper::getResponse([
            "message" => trans("api.service.request_rejected"),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function rate(Request $request)
    {
        $this->validate(
            $request,
            [
                "rating" => "required|integer|in:1,2,3,4,5",
                "comment" => "max:255",
            ],
            ["comment.max" => "character limit should not exceed 255"]
        );
        try {
            $serviceRequestid = $request->id;
            $serviceRequest = ServiceRequest::where("id", $serviceRequestid)
                // ->where('status', 'COMPLETED')
                ->first();
            if ($serviceRequest != null) {
                $paymode = isset($serviceRequest->payment_mode)
                    ? $serviceRequest->payment_mode
                    : "";
                $requestStatus = isset($serviceRequest->status)
                    ? $serviceRequest->status
                    : "";
                if (
                    ($paymode == "CASH" && $requestStatus == "COMPLETED") ||
                    ($paymode != "CASH" && $requestStatus == "DROPPED") ||
                    ($paymode != "CASH" && $requestStatus == "COMPLETED")
                ) {
                    $ratingRequest = Rating::where(
                        "request_id",
                        $serviceRequestid
                    )
                        ->where("admin_service", "SERVICE")
                        ->first();

                    if ($ratingRequest == null) {
                        Rating::create([
                            "company_id" => Auth::guard("provider")->user()
                                ->company_id,
                            "admin_service" => "SERVICE",
                            "provider_id" => $serviceRequest->provider_id,
                            "user_id" => $serviceRequest->user_id,
                            "request_id" => $serviceRequest->id,
                            "provider_rating" => $request->rating,
                            "provider_comment" => $request->comment,
                        ]);
                    } else {
                        Rating::where("request_id", $serviceRequestid)
                            ->where("admin_service", "SERVICE")
                            ->update([
                                "provider_rating" => $request->rating,
                                "provider_comment" => $request->comment,
                            ]);
                    }
                    $serviceRequest->provider_rated = 1;
                    $serviceRequest->save();
                    // Delete from filter so that it doesn't show up in status checks.
                    $user_request = UserRequest::where(
                        "request_id",
                        $request->id
                    )
                        ->where("admin_service", "SERVICE")
                        ->first();
                    if ($user_request != null) {
                        RequestFilter::where(
                            "request_id",
                            $user_request->id
                        )->delete();
                        $user_request->delete();
                    }
                    $provider = Provider::find($serviceRequest->provider_id);
                    // Send Push Notification to Provider
                    $average = Rating::where(
                        "provider_id",
                        $serviceRequest->provider_id
                    )->avg("provider_rating");

                    $provider->is_assigned = 0;
                    $provider->save();

                    $serviceRequest->user->update(["rating" => $average]);

                    // (new SendPushNotification)->Rate($serviceRequest, 'service', 'Service Rated');

                    return Helper::getResponse([
                        "message" => trans("api.service.request_completed"),
                    ]);
                } else {
                    return Helper::getResponse([
                        "status" => 500,
                        "message" => trans("api.service.request_inprogress"),
                        "error" => trans("api.service.request_inprogress"),
                    ]);
                }
            } else {
                return Helper::getResponse([
                    "status" => 500,
                    "message" => trans("api.ride.no_service_found"),
                    "error" => trans("api.ride.no_service_found"),
                ]);
            }
        } catch (ModelNotFoundException $e) {
            return Helper::getResponse([
                "status" => 500,
                "message" => trans("api.service.request_not_completed"),
                "error" => trans("api.service.request_not_completed"),
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function historyList(Request $request, $type)
    {
        try {
            $jsonResponse = [];
            $jsonResponse["type"] = "service";
            $request->request->add(["admin_service" => "Service"]);
            $withCallback = [
                "payment" => function ($query) {
                    $query->select(
                        "id",
                        "service_request_id",
                        "total",
                        "round_of",
                        "cash",
                        "card",
                        "payment_mode",
                        "payable"
                    );
                },
                "service" => function ($query) {
                    $query->select("id", "service_category_id", "service_name");
                },
                "user" => function ($query) {
                    $query->select(
                        "id",
                        "first_name",
                        "last_name",
                        "rating",
                        "currency_symbol"
                    );
                },
                "rating",
            ];
            $ProviderRequests = ServiceRequest::select(
                "id",
                "booking_id",
                "user_id",
                "provider_id",
                "service_id",
                "company_id",
                "s_address",
                "started_at",
                "status",
                "assigned_at",
                "timezone",
                "created_at"
            );

            $data = (new ProviderServices())->providerHistory(
                $request,
                $ProviderRequests,
                $withCallback,
                $type
            );

            $jsonResponse["total_records"] = count($data);

            $jsonResponse["service"] = $data;
            return Helper::getResponse(["data" => $jsonResponse]);
        } catch (Exception $e) {
            Log::info($e);

            return response()->json([
                "error" => trans("api.something_went_wrong"),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function getServiceHistorydetails(Request $request, $id)
    {
        try {
            $jsonResponse = [];
            $jsonResponse["type"] = "service";
            $providerrequest = ServiceRequest::with([
                "payment" => function ($query) {
                    $query->select(
                        "id",
                        "service_request_id",
                        "total",
                        "round_of",
                        "payment_mode",
                        "fixed",
                        "tax",
                        "minute",
                        "extra_charges",
                        "total",
                        "tips",
                        "payable",
                        "wallet",
                        "discount",
                        "cash",
                        "card"
                    );
                },
                "service" => function ($query) {
                    $query->select("id", "service_category_id", "service_name");
                },
                "service.serviceCategory" => function ($query) {
                    $query->select("id", "service_category_name");
                },
                "serviceCategory" => function ($query) {
                    $query->select("id", "service_category_name");
                },
                "user" => function ($query) {
                    $query->select(
                        "id",
                        "first_name",
                        "last_name",
                        "rating",
                        "picture",
                        "mobile",
                        "currency_symbol"
                    );
                },
                "dispute" => function ($query) {
                    $query->where("dispute_type", "provider");
                },
                "rating" => function ($query) {
                    $query->where("admin_service", "SERVICE");
                },
            ])->select(
                "id",
                "booking_id",
                "user_id",
                "provider_id",
                "service_id",
                "company_id",
                "before_image",
                "after_image",
                "currency",
                "s_address",
                "started_at",
                "status",
                "timezone"
            );
            $request->request->add(["admin_service" => "SERVICE", "id" => $id]);
            $data = (new ProviderServices())->providerTripsDetails(
                $request,
                $providerrequest
            );
            $jsonResponse["service"] = $data;
            return Helper::getResponse(["data" => $jsonResponse]);
        } catch (Exception $e) {
            return response()->json([
                "error" => trans("api.something_went_wrong"),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function getServiceRequestDispute(Request $request, $id): JsonResponse
    {
        $ride_request_dispute = ServiceRequestDispute::where(
            "company_id",
            Auth::guard("provider")->user()->company_id
        )
            ->where("service_request_id", $id)
            ->where("dispute_type", "provider")
            ->first();
        return Helper::getResponse(["data" => $ride_request_dispute]);
    }

    /**
     * Display a listing of the resource.
     * @throws ValidationException
     */
    public function saveServiceRequestDispute(Request $request): JsonResponse
    {
        $this->validate($request, [
            "id" => "required",
            "user_id" => "required",
            "provider_id" => "required",
            "dispute_name" => "required",
            "dispute_type" => "required",
        ]);
        $service_request_dispute = ServiceRequestDispute::where(
            "company_id",
            Auth::guard("provider")->user()->company_id
        )
            ->where("service_request_id", $request->id)
            ->where("dispute_type", "provider")
            ->first();
        $request->request->add(["admin_service" => "SERVICE"]);
        if ($service_request_dispute == null) {
            try {
                $disputeRequest = new ServiceRequestDispute();
                $data = (new ProviderServices())->providerDisputeCreate(
                    $request,
                    $disputeRequest
                );
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
        } else {
            return Helper::getResponse([
                "status" => 404,
                "message" => trans(
                    "Already Dispute Created for the Service Request"
                ),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function getdisputedetails(Request $request): JsonResponse
    {
        $dispute = Dispute::select("id", "dispute_name", "service")
            ->where("service", "SERVICE")
            ->where("dispute_type", "provider")
            ->get();
        return Helper::getResponse(["data" => $dispute]);
    }

    /**
     * Display a listing of the resource.
     */
    public function fareTypeList(Request $request): JsonResponse
    {
        $provider_service = ProviderService::where(
            "service_id",
            $request->id
        )->first();

        $selectedFareType = $provider_service->fare_type ?? "FIXED";

        $fareType = [
            "FIXED" => "FIXED",
            "HOURLY" => "HOURLY",
        ];
        return Helper::getResponse([
            "data" => [
                "fareType" => $fareType,
                "selectedFareType" => $selectedFareType,
            ],
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function categoriesList(Request $request)
    {
        $service = Service::with("serviceCategory")
            ->where("id", $request->id)
            ->first();
        $serviceCategory = $service->serviceCategory->id ?? "";

        $Category = ServiceCategory::select(
            "id",
            "service_category_name",
            "service_category_status"
        )
            ->where("service_category_status", 1)
            ->get();
        foreach ($Category as $key => $value) {
            if ($value->id == $serviceCategory) {
                $value->is_selected = 1;
            } else {
                $value->is_selected = 0;
            }
        }
        return Helper::getResponse(["data" => $Category]);
    }

    /**
     * Display a listing of the resource.
     */
    public function subCategoriesList(Request $request, $id)
    {
        $service = Service::with("servicesubCategory")
            ->where("id", $request->service_id)
            ->first();
        $serviceSubCategory = $service->servicesubCategory->id ?? "";

        $subcategory = ServiceSubCategory::where("service_category_id", $id)
            ->where("service_subcategory_status", 1)
            ->get();
        foreach ($subcategory as $key => $value) {
            if ($value->id == $serviceSubCategory) {
                $value->is_selected = 1;
            } else {
                $value->is_selected = 0;
            }
        }
        return Helper::getResponse(["data" => $subcategory]);
    }

    /**
     * Display a listing of the resource.
     */
    public function servicesList(Request $request, $id, $subcategoryid)
    {
        $service = Service::where("id", $request->service_id)->first();

        $serviceId = $service->id ?? "";

        if ($request->is_edit == true) {
            $services = Service::where("service_category_id", $id)
                ->where("service_subcategory_id", $subcategoryid)
                //->where('service_status',1)
                ->get();
            foreach ($services as $key => $value) {
                if ($value->id == $serviceId) {
                    $value->is_selected = 1;
                } else {
                    $value->is_selected = 0;
                }
            }

            return Helper::getResponse(["data" => $services]);
        } else {
            $services = Service::where("service_category_id", $id)
                ->where("service_subcategory_id", $subcategoryid)
                ->where("service_status", 1)
                ->get();
            foreach ($services as $key => $value) {
                if ($value->id == $serviceId) {
                    $value->is_selected = 1;
                } else {
                    $value->is_selected = 0;
                }
            }

            return Helper::getResponse(["data" => $services]);
        }
    }
}

<?php

namespace App\Services\V2;

use App\Helpers\Helper;
use App\Models\Common\PeakHour;
use App\Models\Common\Setting;
use App\Models\Transport\RideCityPrice;
use App\Models\Transport\RidePeakPrice;
use Auth;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Lang;
use Validator;

class ServiceTypes
{
    public function __construct()
    {
    }

    /**
     * get the btc details.
     * get the currency master data.
     * get the payment methods master data.
     * @return response with data,system related errors
     */
    public function show()
    {
    }

    /**
     * get all details.
     * @return response with data,system related errors
     */
    public function getAll()
    {
    }

    /**
     * find tradepost.
     * @param  $id
     * @return response with data,system related errors
     */

    public function find($id)
    {
    }

    /**
     * insert function
     * checking form field validations
     * @param  $postrequest
     * @return response with success,errors,system related errors
     */
    public function create($request)
    {
    }

    /**
     * update function
     * checking form validations
     * @param  $postrequest
     * @return response with success,errors,system related errors
     */
    public function update($request, $id)
    {
    }

    /**
     * delete function.
     * @param  $id
     * @return response with success,errors,system related errors
     */
    public function delete($id)
    {
    }

    public function calculateFare($request, $cflag = 0)
    {
        try {
            $total = $tax_price = "";
            $location = $this->getLocationDistance($request);

            $settings = json_decode(
                json_encode(
                    Setting::where(
                        "company_id",
                        $request["company_id"]
                    )->first()->settings_data
                )
            );

            $siteConfig = $settings->site;
            $transportConfig = $settings->transport;

            $ride_city_price = RideCityPrice::where(
                "city_id",
                $request["city_id"]
            )
                ->where("ride_category", $request["ride_selected"])
                ->where("ride_packages_id", $request["package_id"])
                ->where("ride_delivery_vehicle_id", $request["service_type"])
                ->first();

            if ($ride_city_price == null) {
                header("Access-Control-Allow-Origin: *");
                header("Access-Control-Allow-Headers: *");
                header("Content-Type: application/json");
                http_response_code(400);
                echo json_encode(
                    Helper::getResponse([
                        "status" => 400,
                        "message" => trans(
                            "user.ride.service_not_available_location"
                        ),
                        "error" => trans(
                            "user.ride.service_not_available_location"
                        ),
                    ])->original
                );
                exit();
            }

            if (!empty($location["errors"])) {
                throw new Exception($location["errors"]);
            } else {
                if ($transportConfig->unit_measurement == "Kms") {
                    $total_kilometer = round($location["meter"] / 1000, 1);
                } //TKM
                else {
                    $total_kilometer = round($location["meter"] / 1609.344, 1);
                } //TMi

                $requestarr["city_id"] = $request["city_id"];
                $requestarr["meter"] = $total_kilometer;
                $requestarr["time"] = $location["time"];
                $requestarr["seconds"] = $location["seconds"];
                $requestarr["kilometer"] = $total_kilometer;
                $requestarr["minutes"] = 0;
                $requestarr["service_type"] = $request["service_type"];
                $requestarr["city_id"] = $request["city_id"];
                $requestarr["ride_selected"] = $request["ride_selected"];
                $requestarr["package_id"] = $request["package_id"];

                $tax_percentage = $ride_city_price->tax; //config('constants.tax_percentage');
                $commission_percentage = $ride_city_price->commission; //config('constants.commission_percentage');
                $surge_trigger = "1"; //config('constants.surge_trigger');

                $price_response = $this->applyPriceLogic($requestarr);

                if ($tax_percentage > 0) {
                    $tax_price = $this->applyPercentage(
                        $price_response["price"],
                        $tax_percentage
                    );
                    $total = $price_response["price"] + $tax_price;
                } else {
                    $total = $price_response["price"];
                }

                if ($cflag != 0) {
                    if ($commission_percentage > 0) {
                        $commission_price = $this->applyPercentage(
                            $price_response["price"],
                            $commission_percentage
                        );
                        $commission_price =
                            $price_response["price"] + $commission_price;
                    }

                    $peak = 0;

                    $start_time = Carbon::now()->toDateTimeString();
                    $end_time = Carbon::now()->toDateTimeString();

                    $peak_percentage = 1 + 0 / 100 . "X";

                    $start_time_check = PeakHour::where(
                        "start_time",
                        "<=",
                        $start_time
                    )
                        ->where("end_time", ">=", $start_time)
                        ->where("company_id", ">=", $request["company_id"])
                        ->first();

                    if ($start_time_check) {
                        $Peakcharges = RidePeakPrice::where(
                            "ride_city_price_id",
                            $request["city_id"]
                        )
                            ->where(
                                "ride_delivery_id",
                                $request["service_type"]
                            )
                            ->where("peak_hour_id", $start_time_check->id)
                            ->first();

                        if ($Peakcharges) {
                            $peak_price =
                                ($Peakcharges->peak_price / 100) * $total;
                            $total += $peak_price;
                            $peak = 1;
                            $peak_percentage =
                                1 + $Peakcharges->peak_price / 100 . "X";
                        }
                    }
                }

                if ($request["ride_selected"] == "DAILY") {
                    $return_data["estimated_fare"] = $this->applyNumberFormat(
                        floatval($total)
                    );
                    $return_data["distance"] = $total_kilometer;
                    $return_data["time"] = $location["time"];
                } elseif ($request["ride_selected"] == "RENTAL") {
                    $return_data["estimated_fare"] = $this->applyNumberFormat(
                        floatval($total)
                    );
                }

                $return_data["tax_price"] = $this->applyNumberFormat(
                    floatval($tax_price)
                );
                $return_data["base_price"] = $this->applyNumberFormat(
                    floatval($price_response["base_price"])
                );
                $return_data["service_type"] = (int)$request["service_type"];
                $return_data["service"] = $price_response["service_type"];

                if (Auth::guard("user")->user()) {
                    $return_data["peak"] = $peak;
                    $return_data["peak_percentage"] = $peak_percentage;
                    $return_data["wallet_balance"] = $this->applyNumberFormat(
                        floatval(Auth::guard("user")->user()->wallet_balance)
                    );
                }

                $service_response["data"] = $return_data;
            }
        } catch (Exception $e) {
            $service_response["errors"] = $e->getMessage();
        }

        return $service_response;
    }

    public function getLocationDistance($locationarr)
    {
        $fn_response = ["data" => null, "errors" => null];

        try {
            $s_latitude = $locationarr["s_latitude"];
            $s_longitude = $locationarr["s_longitude"];
            $d_latitude = empty($locationarr["d_latitude"])
                ? $locationarr["s_latitude"]
                : $locationarr["d_latitude"];
            $d_longitude = empty($locationarr["d_longitude"])
                ? $locationarr["s_longitude"]
                : $locationarr["d_longitude"];

            $apiurl =
                "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" .
                $s_latitude .
                "," .
                $s_longitude .
                "&destinations=" .
                $d_latitude .
                "," .
                $d_longitude .
                "&mode=driving&sensor=false&units=imperial&key=" .
                $locationarr["server_key"];

            $client = new Client();
            $location = $client->get($apiurl);
            $location = json_decode($location->getBody(), true);

            if (
                !empty($location["rows"][0]["elements"][0]["status"]) &&
                $location["rows"][0]["elements"][0]["status"] == "ZERO_RESULTS"
            ) {
                throw new Exception("Out of service area", 1);
            }
            $fn_response["meter"] =
                $location["rows"][0]["elements"][0]["distance"]["value"];
            $fn_response["time"] =
                $location["rows"][0]["elements"][0]["duration"]["text"];
            $fn_response["seconds"] =
                $location["rows"][0]["elements"][0]["duration"]["value"];
        } catch (Exception $e) {
            $fn_response["errors"] = trans("user.maperror");
        }

        return $fn_response;
    }

    public function applyPriceLogic($requestarr, $iflag = 0)
    {
        $fn_response = [];

        $ride_city_price = RideCityPrice::where(
            "city_id",
            $requestarr["city_id"]
        )
            ->where("ride_category", $requestarr["ride_selected"])
            ->where("ride_packages_id", $requestarr["package_id"])
            ->where("ride_delivery_vehicle_id", $requestarr["service_type"])
            ->first();

        if ($ride_city_price == null) {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Headers: *");
            header("Content-Type: application/json");
            http_response_code(400);
            echo json_encode(
                Helper::getResponse([
                    "status" => 400,
                    "message" => trans(
                        "user.ride.service_not_available_location"
                    ),
                    "error" => trans(
                        "user.ride.service_not_available_location"
                    ),
                ])->original
            );
            exit();
        }

        $fn_response["service_type"] = $requestarr["service_type"];

        if ($iflag == 0) {
            //for estimated fare
            $total_kilometer = $requestarr["meter"]; //TKM || TMi
            $total_minutes = round($requestarr["seconds"] / 60); //TM
            $total_hours = $requestarr["seconds"] / 60 / 60; //TH
        } else {
            //for invoice fare
            $total_kilometer = $requestarr["kilometer"]; //TKM || TMi
            $total_minutes = $requestarr["minutes"]; //TM
            $total_hours = $requestarr["minutes"] / 60; //TH
        }

        $per_minute = $ride_city_price == null ? 0 : $ride_city_price->minute; //PM
        $per_hour = $ride_city_price == null ? 0 : $ride_city_price->hour; //PH
        $per_kilometer = $ride_city_price == null ? 0 : $ride_city_price->price; //PKM
        $base_distance =
            $ride_city_price == null ? 0 : $ride_city_price->distance; //BD
        $base_price = $ride_city_price == null ? 0 : $ride_city_price->fixed; //BP
        $price = 0;
        if ($ride_city_price != null) {
            if ($ride_city_price->calculator == "MIN") {
                //BP+(TM*PM)
                $price = $base_price + $total_minutes * $per_minute;
            } elseif ($ride_city_price->calculator == "HOUR") {
                //BP+(TH*PH)
                $price = $base_price + $total_hours * $per_hour;
            } elseif ($ride_city_price->calculator == "DISTANCE") {
                //BP+((TKM-BD)*PKM)
                if ($base_distance > $total_kilometer) {
                    $price = $base_price;
                } else {
                    $price =
                        $base_price +
                        ($total_kilometer - $base_distance) * $per_kilometer;
                }
            } elseif ($ride_city_price->calculator == "DISTANCEMIN") {
                //BP+((TKM-BD)*PKM)+(TM*PM)
                if ($base_distance > $total_kilometer) {
                    $price = $base_price + $total_minutes * $per_minute;
                } else {
                    $price =
                        $base_price +
                        (($total_kilometer - $base_distance) * $per_kilometer +
                            $total_minutes * $per_minute);
                }
            } elseif ($ride_city_price->calculator == "DISTANCEHOUR") {
                //BP+((TKM-BD)*PKM)+(TH*PH)
                if ($base_distance > $total_kilometer) {
                    $price = $base_price + $total_hours * $per_hour;
                } else {
                    $price =
                        $base_price +
                        (($total_kilometer - $base_distance) * $per_kilometer +
                            $total_hours * $per_hour);
                }
            } elseif ($ride_city_price->calculator == "RENTAL") {
                //BP+(EXTRA HOUR)+(EXTRA DISTANCE)
                $extra_price = 0;

                if ($ride_city_price->hour < $total_hours) {
                    //Hour Price
                    $extra_price +=
                        $ride_city_price->minute *
                        ceil(($total_hours - $ride_city_price->hour) / 60);
                }

                if ($ride_city_price->distance < $total_kilometer) {
                    //Distance
                    $extra_price +=
                        $ride_city_price->price *
                        ($total_kilometer - $ride_city_price->distance);
                }

                $price = $base_price + $extra_price;
            } else {
                //by default set Ditance price BP+((TKM-BD)*PKM)
                $price =
                    $base_price +
                    ($total_kilometer - $base_distance) * $per_kilometer;
            }
        }

        $fn_response["price"] = $price;
        $fn_response["base_price"] = $base_price;
        if ($base_distance > $total_kilometer) {
            $fn_response["distance_fare"] = 0;
        } else {
            $fn_response["distance_fare"] =
                ($total_kilometer - $base_distance) * $per_kilometer;
        }
        $fn_response["minute_fare"] = $total_minutes * $per_minute;
        $fn_response["hour_fare"] = $total_hours * $per_hour;
        $fn_response["calculator"] =
            $ride_city_price == null ? null : $ride_city_price->calculator;
        $fn_response["ride_city_price"] = $ride_city_price;

        return $fn_response;
    }

    public function applyPercentage($total, $percentage)
    {
        return ($percentage / 100) * $total;
    }

    public function applyNumberFormat($total)
    {
        return round($total, config("constants.round_decimal"));
    }

    /**
     * Get a validator for a tradepost.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            "location" => "required",
        ];

        $messages = [
            "location.required" => "Location Required!",
        ];

        return Validator::make($data, $rules, $messages);
    }
}

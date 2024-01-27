<?php

namespace App\Services;

use App\Models\Common\PaymentLog;
use App\Models\Common\Setting;
use Exception;
use Stripe\Stripe;


class PaymentGateway
{
    private string $gateway;

    public function __construct($gateway)
    {
        $this->gateway = strtoupper($gateway);
    }

    public function process($attributes)
    {
        $gateway = $this->gateway == "STRIPE" ? "CARD" : $this->gateway;

        $log = PaymentLog::where("transaction_code", $attributes["order"])
            ->where("payment_mode", $gateway)
            ->first();

        if ($this->gateway === "STRIPE") {
            try {
                $settings = json_decode(
                    json_encode(Setting::first()->settings_data)
                );
                $paymentConfig = json_decode(
                    json_encode($settings->payment),
                    true
                );

                $cardObject = array_values(
                    array_filter($paymentConfig, function ($e) {
                        return $e["name"] == "card";
                    })
                );

                $stripe_secret_key = "";

                if (!empty($cardObject)) {
                    $stripeSecretObject = array_values(
                        array_filter(
                            $cardObject[0]["credentials"],
                            function ($e) {
                                return $e["name"] == "stripe_secret_key";
                            }
                        )
                    );

                    if (!empty($stripeSecretObject)) {
                        $stripe_secret_key =
                            $stripeSecretObject[0]["value"];
                    }
                }

                Stripe::setApiKey($stripe_secret_key);
                $charge = \Stripe\Charge::create([
                    "amount" => $attributes["amount"] * 100,
                    "currency" => $attributes["currency"],
                    "customer" => $attributes["customer"],
                    "card" => $attributes["card"],
                    "description" => $attributes["description"],
                    "receipt_email" => $attributes["receipt_email"],
                ]);
                $log->response = json_encode($charge);
                $log->save();

                $paymentId = $charge["id"];

                return (object)[
                    "status" => "SUCCESS",
                    "payment_id" => $paymentId,
                ];
            } catch (\Throwable $e) {

                return (object)[
                    "status" => "FAILURE",
                    "message" => $e->getMessage(),
                ];
            } catch (Exception $e) {
                return (object)[
                    "status" => "FAILURE",
                    "message" => $e->getMessage(),
                ];
            }
        }
    }
}

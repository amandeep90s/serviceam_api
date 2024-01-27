<?php

namespace App\Http\Controllers\Common;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Common\Card;
use App\Models\Common\PaymentLog;
use App\Models\Common\Provider;
use App\Models\Common\ProviderCard;
use App\Models\Common\User;
use App\Services\PaymentGateway;
use App\Services\SendPushNotification;
use App\Services\Transactions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    /**
     * add wallet money for user.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function add_money(Request $request): JsonResponse
    {
        $this->validate($request, [
            "user_type" => "required",
            "amount" => "required",
            "payment_mode" => "required",
        ]);

        $random = "TRNX" . mt_rand(100000, 999999);

        $user_type = $request->user_type;

        $log = new PaymentLog();
        $log->user_type = $user_type;
        $log->admin_service = "WALLET";
        $log->is_wallet = "1";
        $log->amount = $request->amount;
        $log->transaction_code = $random;
        $log->payment_mode = strtoupper($request->payment_mode);
        $log->user_id = Auth::guard($user_type)->user()->id;
        $log->company_id = Auth::guard($user_type)->user()->company_id;
        $log->save();

        switch (strtoupper($request->payment_mode)) {
            case "BRAINTREE":
                $gateway = new PaymentGateway("braintree");

                return Helper::getResponse([
                    "data" => ["gateway" => $gateway->process([
                        "amount" => $request->amount,
                        "nonce" => $request->braintree_nonce,
                        "order" => $random,
                    ])],
                    "message" => trans("api.amount_added_to_your_wallet"),
                ]);

            case "CARD":
                if ($user_type == "provider") {
                    ProviderCard::where(
                        "provider_id",
                        Auth::guard("provider")->user()->id
                    )->update(["is_default" => 0]);
                    ProviderCard::where("card_id", $request->card_id)->update([
                        "is_default" => 1,
                    ]);
                } else {
                    Card::where(
                        "user_id",
                        Auth::guard("user")->user()->id
                    )->update(["is_default" => 0]);
                    Card::where("card_id", $request->card_id)->update([
                        "is_default" => 1,
                    ]);
                }

                $gateway = new PaymentGateway("stripe");
                $response = $gateway->process([
                    "order" => $random,
                    "amount" => $request->amount,
                    "currency" => "USD",
                    "customer" => Auth::guard($user_type)->user()->stripe_cust_id,
                    "card" => $request->card_id,
                    "description" => "Adding Money for " .
                        Auth::guard($user_type)->user()->email,
                    "receipt_email" => Auth::guard($user_type)->user()->email,
                ]);
                if ($response->status == "SUCCESS") {
                    if ($user_type == "user") {
                        //create transaction to user wallet
                        $transaction["id"] = Auth::guard("user")->user()->id;
                        $transaction["amount"] = $log->amount;
                        $transaction["company_id"] = $log->company_id;
                        (new Transactions())->userCreditDebit($transaction, 1);

                        //update wallet balance
                        $wallet_balance =
                            Auth::guard("user")->user()->wallet_balance +
                            $log->amount;
                        User::where("id", Auth::guard("user")->user()->id)
                            ->where(
                                "company_id",
                                Auth::guard("user")->user()->company_id
                            )
                            ->update(["wallet_balance" => $wallet_balance]);

                        (new SendPushNotification())->WalletMoney(
                            Auth::guard("user")->user()->id,
                            $wallet_balance,
                            "common",
                            "Wallet amount added",
                            ["amount" => $wallet_balance]
                        );
                    } else {
                        //create transaction to provider wallet
                        $transaction["id"] = Auth::guard(
                            "provider"
                        )->user()->id;
                        $transaction["amount"] = $log->amount;
                        $transaction["company_id"] = $log->company_id;
                        (new Transactions())->providerCreditDebit(
                            $transaction,
                            1
                        );

                        //update wallet balance
                        $wallet_balance =
                            Auth::guard("provider")->user()->wallet_balance +
                            $log->amount;

                        Provider::where(
                            "id",
                            Auth::guard("provider")->user()->id
                        )
                            ->where(
                                "company_id",
                                Auth::guard("provider")->user()->company_id
                            )
                            ->update(["wallet_balance" => $wallet_balance]);

                        (new SendPushNotification())->ProviderWalletMoney(
                            Auth::guard("provider")->user()->id,
                            $wallet_balance,
                            "common",
                            "Wallet amount added",
                            ["amount" => $wallet_balance]
                        );
                    }

                    return Helper::getResponse([
                        "data" => ["wallet_balance" => $wallet_balance],
                        "message" => trans("api.amount_added_to_your_wallet"),
                    ]);
                } else {
                    return Helper::getResponse([
                        "status" => "500",
                        "message" => trans("Transaction Failed"),
                    ]);
                }
        }
    }
}

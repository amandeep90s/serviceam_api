<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use App\Models\Common\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DBClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronjob:demoData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clearing the demo data weekly basics';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $setting = Setting::where('company_id', Auth::guard(Helper::getGuard())->user()->company_id)->first();

        if ($setting->clear_seed == 1) {

            Log::info('demo data deleting');
            $userValues = array('stripe_cust_id' => NULL, 'wallet_balance' => 0, 'rating' => 5);
            DB::table('users')->where('id', '>', 30)->delete();
            DB::table('users')->update($userValues);
            DB::table('password_resets')->delete();
            DB::table('cards')->delete();
            DB::table('user_wallet')->delete();
            DB::table('providers')->where('id', '>', 30)->delete();
            DB::table('providers')->update($userValues);
            DB::table('provider_cards')->delete();
            DB::table('provider_devices')->where('provider_id', '>', 30)->delete();
            DB::table('provider_profiles')->where('provider_id', '>', 30)->delete();
            DB::table('provider_documents')->where('provider_id', '>', 30)->delete();
            DB::table('provider_services')->where('provider_id', '>', 30)->delete();
            DB::table('provider_wallet')->delete();
            DB::table('admins')->where('id', '>', 1)->delete();
            DB::table('admin_wallet')->delete();
            DB::table('fleets')->where('id', '>', 1)->delete();
            $otherValues = array('stripe_cust_id' => NULL, 'wallet_balance' => 0);
            DB::table('fleets')->update($otherValues);
            DB::table('fleet_password_resets')->delete();
            DB::table('fleet_cards')->delete();
            DB::table('fleet_wallet')->delete();
            DB::table('accounts')->where('id', '>', 1)->delete();
            DB::table('account_password_resets')->delete();
            DB::table('dispatchers')->where('id', '>', 1)->delete();
            DB::table('dispatcher_password_resets')->delete();

            //other tables
            DB::table('custom_pushes')->delete();
            DB::table('favourite_locations')->delete();
            DB::table('promocodes')->delete();
            DB::table('promocode_usages')->delete();
            DB::table('request_filters')->delete();
            DB::table('user_requests')->delete();
            DB::table('payrolls')->delete();
            DB::table('notifications')->delete();

            DB::table('service_requests')->delete();
            DB::table('service_request_payments')->delete();
            DB::table('service_request_disputes')->delete();
        }
    }
}

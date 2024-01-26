<?php

namespace App\Console\Commands;

use App\Models\Common\Provider;
use App\Notifications\SendPushNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProviderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronjob:providers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the provider status';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $Providers = Provider::with('service')
            ->whereHas('service', function ($query) {
                $query->where('status', 'active');
            })
            ->where('updated_at', '<=', Carbon::now()->subMinutes(10))->get();

        if (!empty($Providers)) {
            foreach ($Providers as $Provider) {
                DB::table('provider_services')->where('provider_id', $Provider->id)->update(['status' => 'hold']);
                //send push to provider
                (new SendPushNotification())->provider_hold($Provider->id);
            }
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Common\NotificationDay;
use App\Models\Common\ProviderDocument;
use App\Notifications\SendPushNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProviderDocumentExpiryNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronjob:providersExpiryDocument';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification for the providers expiry document';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $notifications = NotificationDay::where('status', 'active')->get();

        foreach ($notifications as $notification) {

            $date = Carbon::today();

            if ($notification->days) {
                $notificationProcessInDays = $date->subDays($notification->days);
                Log::info("notificationProcessInDays:: " . $notificationProcessInDays);

                $providerDocuments = ProviderDocument::with('document')
                    ->whereDate('expires_at', $notificationProcessInDays)
                    ->get();

                //Send notification to provider
                $this->sendpushnotify($providerDocuments);

                //To update the status as InActive when the providers are not update expiry date
                $this->updateDocumentStatus();
            }
        }
    }

    protected function sendpushnotify($providerDocuments): void
    {
        foreach ($providerDocuments as $document) {
            (new SendPushNotification())->provider_documents_notify($document->provider_id);
        }
    }

    protected function updateDocumentStatus()
    {
        $response = ProviderDocument::whereDate('expires_at', '>', Carbon::today())
            ->where('status', 'ACTIVE')
            //->where('provider_id',$provider_id)
            ->update([
                'status' => 'ASSESSING'
            ]);
        Log::info("updateDocumentStatus with ASSESSING " . $response);

        return $response;
    }
}

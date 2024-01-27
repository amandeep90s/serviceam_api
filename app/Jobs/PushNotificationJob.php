<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $topic;
    protected $push_message;
    protected $title;
    protected $data;
    protected $user;
    protected $settings;
    protected $type;

    /**
     * Create a new job instance.
     */
    public function __construct($topic, $push_message, $title, $data, $user, $settings, $type)
    {
        $this->topic = $topic;
        $this->push_message = $push_message;
        $this->title = $title;
        $this->data = $data;
        $this->user = $user;
        $this->settings = $settings;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->user->device_type == 'IOS') {

            if ($this->type == 'user') {
                $pem = app()->basePath('storage/app/public/' . $this->user->company_id . '/apns') . '/user.pem';
            } else {
                $pem = app()->basePath('storage/app/public/' . $this->user->company_id . '/apns') . '/provider.pem';
            }

            if (file_exists($pem)) {
                $config = [
                    'environment' => $this->settings->site->environment,
                    'certificate' => app()->basePath('storage/app/public/' . $this->user->company_id . '/apns') . '/user.pem',
                    'passPhrase' => $this->settings->site->ios_push_password,
                    'service' => 'apns'
                ];
            }
        } elseif ($this->user->device_type == 'ANDROID') {

            if ($this->settings->site->android_push_key != "") {
                $config = [
                    'environment' => $this->settings->site->environment,
                    'apiKey' => $this->settings->site->android_push_key,
                    'service' => 'gcm'
                ];
            }
        }
    }
}

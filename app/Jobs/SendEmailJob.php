<?php

namespace App\Jobs;

use App\Helpers\Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $error;
    protected $company;
    protected $emails;

    /**
     * Create a new job instance.
     */
    public function __construct($error, $company, $emails)
    {
        $this->error = $error;
        $this->company = $company;
        $this->emails = $emails;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $subject = '500 Error in ' . $this->company;
        $templateFile = 'mails/errormail';
        $data = ['body' => $this->error];

        if (count($this->emails) > 0) {
            Helper::send_emails_job($templateFile, $this->emails, $subject, $data);
        }
    }
}

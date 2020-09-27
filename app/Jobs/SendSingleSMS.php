<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mavinoo\LaravelSmsIran\Laravel\Facade\Sms;

class SendSingleSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $smsData;

    public function __construct($smsData)
    {
        $this->smsData = $smsData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->smsData) {
            Sms::sendSMS([$this->smsData['cell']] , $this->smsData['text']);
        }
    }
}

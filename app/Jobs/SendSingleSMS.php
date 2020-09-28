<?php

namespace App\Jobs;

use App\Services\SmsIR_VerificationCode;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

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
            try {
                Log::info("start to send sms Data :".$this->smsData['text'].' - '.$this->smsData['cell']);
                $APIKey = "bb30d04e11dd65e1d0d2d04e";
                $SecretKey = "3992@ala@pass";
                $APIURL = "https://RestfulSms.com/";
                $SmsIR_VerificationCode = new SmsIR_VerificationCode($APIKey, $SecretKey, $APIURL);
                $SmsIR_VerificationCode->verificationCode($this->smsData['text'], $this->smsData['cell']);

            } catch (\Exception $e) {
                Log::info("sms send error for mobile number ");
            }
        }
    }
}

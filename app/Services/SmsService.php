<?php
namespace App\Services;

use App\Models\SMS;

class SMSService
{
    protected $smsModel;

    public function __construct()
    {
        $this->smsModel = new SMS();
    }

    public function sendSMS($userId, $phone, $message, $provider = 'local')
    {
        // provider abstraction placeholder — mark as sent
        $this->smsModel->log($userId, $phone, $message, $provider, 'sent');
        return true;
    }

    public function sendRepairReady($userId, $phone, $repairId)
    {
        $msg = "تعمیر خودرو شما (#{$repairId}) آماده تحویل است.";
        return $this->sendSMS($userId, $phone, $msg);
    }

    public function sendPaymentNotice($userId, $phone, $amount)
    {
        $msg = "پرداخت شما به مبلغ {$amount} ثبت شد.";
        return $this->sendSMS($userId, $phone, $msg);
    }
}


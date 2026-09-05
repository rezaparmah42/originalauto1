<?php
namespace App\Services;

use App\Models\Notification;
use App\Models\RepairUpdate;

class NotificationService
{
    protected $notificationModel;
    protected $repairUpdateModel;

    public function __construct()
    {
        $this->notificationModel = new Notification();
        $this->repairUpdateModel = new RepairUpdate();
    }

    public function notifyUser($userId, $type, $title, $message)
    {
        return $this->notificationModel->create($userId, $type, $title, $message);
    }

    public function notifyRepairStatus($repairId, $userId, $status, $title = '', $desc = '')
    {
        $this->repairUpdateModel->addUpdate($repairId, $status, $title ?: "به‌روزرسانی تعمیر: {$status}", $desc, $userId);
        return $this->notificationModel->create($userId, 'repair_update', $title ?: 'آپدیت تعمیر', $desc ?: "تعمیر #{$repairId} وضعیت: {$status}");
    }

    public function notifyBooking($userId, $bookingId, $title, $message)
    {
        return $this->notificationModel->create($userId, 'booking', $title, $message);
    }

    public function notifyPayment($userId, $paymentId, $title, $message)
    {
        return $this->notificationModel->create($userId, 'payment', $title, $message);
    }
}

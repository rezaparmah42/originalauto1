<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Notification;
use App\Models\SMS;

class AdminCommunicationController extends Controller
{
    private $notificationModel;
    private $smsModel;

    public function __construct()
    {
        $this->notificationModel = new Notification();
        $this->smsModel = new SMS();
    }

    public function notifications()
    {
        requireLogin(); $this->requireAdminAccess();
        $items = $this->notificationModel->getUserNotifications(0, 200);
        $this->view('admin/communication/notifications', ['items' => $items]);
    }

    public function sms()
    {
        requireLogin(); $this->requireAdminAccess();
        $items = $this->smsModel->history(0, 200);
        $this->view('admin/communication/sms', ['items' => $items]);
    }

    private function requireAdminAccess()
    {
        if (!isAdmin()) {
            error('دسترسی مدیریتی لازم است.');
            redirect(SITE_URL . '/admin/login');
        }
    }
}

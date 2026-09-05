<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    private $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new Notification();
    }

    public function index()
    {
        requireCustomer();
        $userId = currentCustomerId();
        $items = $this->notificationModel->getUserNotifications($userId);
        $this->view('notifications/index', ['items' => $items]);
    }

    public function read($id)
    {
        requireCustomer();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/notifications');
        }

        $notification = $this->notificationModel->findForUser((int) $id, currentCustomerId());
        if (!$notification) {
            error('اعلان مورد نظر یافت نشد.');
            redirect(SITE_URL . '/notifications');
        }

        $this->notificationModel->markReadForUser((int) $id, currentCustomerId());
        success('اعلان به‌عنوان خوانده‌شده علامت‌گذاری شد.');
        redirect(SITE_URL . '/notifications');
    }
}

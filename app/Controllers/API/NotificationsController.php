<?php

namespace App\Controllers\API;

use App\Core\Controller;
use App\Core\APIResponse;
use App\Middleware\APIMiddleware;
use App\Models\Notification;

class NotificationsController extends Controller
{
    public function index()
    {
        $token = APIMiddleware::protect();
        $notificationModel = new Notification();
        $notifications = $notificationModel->getUserNotifications((int) $token['user_id']);

        APIResponse::success(['notifications' => $notifications]);
    }

    public function markRead()
    {
        $token = APIMiddleware::protect();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            APIResponse::error('Method not allowed', 405);
        }

        $data = APIMiddleware::getRequestData();
        $notificationId = (int) ($data['notification_id'] ?? 0);

        if ($notificationId <= 0) {
            APIResponse::error('Notification id is required.', 422);
        }

        $notificationModel = new Notification();
        $notification = $notificationModel->findById($notificationId);
        if (!$notification || (int) ($notification['user_id'] ?? 0) !== (int) $token['user_id']) {
            APIResponse::error('Notification access denied.', 403);
        }

        $notificationModel->markRead($notificationId);
        APIResponse::success(['message' => 'Notification marked as read']);
    }
}

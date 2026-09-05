<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ChatMessage;

class ChatController extends Controller
{
    private $chatModel;

    public function __construct()
    {
        $this->chatModel = new ChatMessage();
    }

    public function conversation($repairId)
    {
        requireLogin();

        $repairId = (int) $repairId;
        $repairModel = new \App\Models\Repair();
        $repair = $repairModel->findById($repairId);

        if (!$repair) {
            error('تعمیر مورد نظر یافت نشد.');
            redirect(SITE_URL . '/account/repairs');
        }

        if (!isAdmin()) {
            requireCustomer();
            if (!$repairModel->isRepairForUser($repairId, currentCustomerId())) {
                error('دسترسی به مکالمه این تعمیر مجاز نیست.');
                redirect(SITE_URL . '/account/repairs');
            }
        }

        $conv = $this->chatModel->getConversation($repairId);
        $this->view('chat/conversation', ['messages' => $conv, 'repair_id' => $repairId]);
    }

    public function send()
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(SITE_URL);
        if (!verify_csrf()) { error('درخواست نامعتبر'); redirect(SITE_URL); }

        $repairId = (int) ($_POST['repair_id'] ?? 0);
        $message = trim((string) ($_POST['message'] ?? ''));
        if ($repairId <= 0 || $message === '') {
            error('اطلاعات پیام نامعتبر است.');
            redirect(SITE_URL . '/account/repairs');
        }

        $repairModel = new \App\Models\Repair();
        $repair = $repairModel->findById($repairId);
        if (!$repair) {
            error('تعمیر مورد نظر یافت نشد.');
            redirect(SITE_URL . '/account/repairs');
        }

        if (!isAdmin()) {
            requireCustomer();
            if (!$repairModel->isRepairForUser($repairId, currentCustomerId())) {
                error('دسترسی به مکالمه این تعمیر مجاز نیست.');
                redirect(SITE_URL . '/account/repairs');
            }
            $senderId = currentCustomerId();
            $senderType = 'customer';
        } else {
            $senderId = $_SESSION['admin']['id'] ?? null;
            $senderType = 'admin';
        }

        if ($senderId === null) {
            error('جلسه کاربری نامعتبر است.');
            redirect(SITE_URL . '/account/repairs');
        }

        $this->chatModel->sendMessage($repairId, $senderId, $senderType, $message);
        redirect(SITE_URL . '/repair/chat/' . $repairId);
    }
}

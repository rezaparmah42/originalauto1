<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\FaultKnowledge;

class AdminAIController extends Controller
{
    private $knowledgeModel;

    public function __construct()
    {
        $this->knowledgeModel = new FaultKnowledge();
    }

    public function index()
    {
        requireLogin();
        $this->requireAdminAccess();

        $items = $this->knowledgeModel->search('', 100);
        $this->view('admin/ai/index', ['items' => $items]);
    }

    public function create()
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                error('درخواست نامعتبر');
                redirect(SITE_URL . '/admin/ai-knowledge');
            }

            $this->knowledgeModel->create([
                'dtc_code' => trim($_POST['dtc_code'] ?? ''),
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'severity' => trim($_POST['severity'] ?? 'unknown'),
                'possible_causes' => trim($_POST['possible_causes'] ?? ''),
                'recommended_actions' => trim($_POST['recommended_actions'] ?? ''),
            ]);

            success('اطلاعات ذخیره شد.');
            redirect(SITE_URL . '/admin/ai-knowledge');
        }

        $this->view('admin/ai/create');
    }

    public function edit($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        if (is_numeric($id)) {
            $item = $this->knowledgeModel->findById((int)$id);
        } else {
            $item = $this->knowledgeModel->findByCode($id);
        }
        if (!$item) {
            error('مورد یافت نشد.');
            redirect(SITE_URL . '/admin/ai-knowledge');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                error('درخواست نامعتبر');
                redirect(SITE_URL . '/admin/ai-knowledge');
            }

            $this->knowledgeModel->update((int) $item['id'], [
                'dtc_code' => trim($_POST['dtc_code'] ?? ''),
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'severity' => trim($_POST['severity'] ?? 'unknown'),
                'possible_causes' => trim($_POST['possible_causes'] ?? ''),
                'recommended_actions' => trim($_POST['recommended_actions'] ?? ''),
            ]);

            success('به‌روزرسانی انجام شد.');
            redirect(SITE_URL . '/admin/ai-knowledge');
        }

        $this->view('admin/ai/edit', ['item' => $item]);
    }

    private function requireAdminAccess()
    {
        if (!isAdmin()) {
            error('دسترسی مدیریتی لازم است.');
            redirect(SITE_URL . '/admin/login');
        }
    }
}

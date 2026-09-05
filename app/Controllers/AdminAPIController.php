<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\APIResponse;
use App\Models\ApiDevice;
use App\Models\ApiLog;
use App\Models\ApiToken;

class AdminAPIController extends Controller
{
    private $apiTokenModel;
    private $apiDeviceModel;
    private $apiLogModel;

    public function __construct()
    {
        $this->apiTokenModel = new ApiToken();
        $this->apiDeviceModel = new ApiDevice();
        $this->apiLogModel = new ApiLog();
    }

    public function devices()
    {
        requireLogin();
        $this->requireAdminAccess();

        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $perPage = isset($_GET['per_page']) ? max(10, (int) $_GET['per_page']) : 25;
        $offset = ($page - 1) * $perPage;

        $filters = [];
        $filters['q'] = trim((string) ($_GET['q'] ?? ''));
        $filters['status'] = $_GET['status'] ?? 'active';

        $total = $this->apiTokenModel->countTokens($filters);
        $tokens = $this->apiTokenModel->listTokens($filters, $perPage, $offset);

        $devices = [];
        foreach ($tokens as $token) {
            $devices[] = [
                'token_id' => $token['id'],
                'user_id' => $token['user_id'],
                'user_name' => $token['user_name'] ?? '',
                'device_name' => $token['device_name'] ?? 'Unknown',
                'platform' => $token['platform'] ?? 'unknown',
                'expires_at' => $token['expires_at'],
                'last_used_at' => $token['last_used_at'],
                'created_at' => $token['created_at'],
                'revoked' => (int) ($token['revoked'] ?? 0),
            ];
        }

        $this->view('admin/api/devices', [
            'devices' => $devices,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'filters' => $filters,
        ]);
    }

    public function revokeToken($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            error('درخواست نامعتبر یا CSRF نامعتبر است.');
            return redirect(SITE_URL . '/admin/api/devices');
        }

        if (!is_numeric($id)) {
            error('شناسه نامعتبر است.');
            return redirect(SITE_URL . '/admin/api/devices');
        }

        $this->apiTokenModel->revokeTokenById((int) $id);
        success('توکن با موفقیت باطل شد.');
        redirect(SITE_URL . '/admin/api/devices');
    }

    public function revokeUserTokens($userId)
    {
        requireLogin();
        $this->requireAdminAccess();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            error('درخواست نامعتبر یا CSRF نامعتبر است.');
            return redirect(SITE_URL . '/admin/api/devices');
        }

        if (!is_numeric($userId)) {
            error('شناسه کاربر نامعتبر است.');
            return redirect(SITE_URL . '/admin/api/devices');
        }

        $this->apiTokenModel->revokeAllUserTokens((int) $userId);
        success('تمام توکن‌های کاربر با موفقیت باطل شد.');
        redirect(SITE_URL . '/admin/api/devices');
    }

    public function logs()
    {
        requireLogin();
        $this->requireAdminAccess();
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $perPage = isset($_GET['per_page']) ? max(10, (int) $_GET['per_page']) : 50;
        $offset = ($page - 1) * $perPage;

        $filters = [];
        $filters['endpoint'] = trim((string) ($_GET['endpoint'] ?? ''));
        $filters['response_code'] = isset($_GET['response_code']) && $_GET['response_code'] !== '' ? (int) $_GET['response_code'] : null;
        $filters['sort'] = $_GET['sort'] ?? 'desc';

        $total = $this->apiLogModel->countFiltered($filters);
        $logs = $this->apiLogModel->listRecentFiltered($filters, $perPage, $offset);

        $this->view('admin/api/logs', [
            'logs' => $logs,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'filters' => $filters,
        ]);
    }

    private function requireAdminAccess()
    {
        if (!isAdmin()) {
            error('دسترسی مدیریتی لازم است.');
            redirect(SITE_URL . '/admin/login');
        }
    }
}

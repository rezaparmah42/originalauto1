<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Diagnostic;

class AdminDiagnosticController extends Controller
{
    private $diagnosticModel;

    public function __construct()
    {
        $this->diagnosticModel = new Diagnostic();
    }

    public function index()
    {
        requireLogin();
        $this->requireAdminAccess();

        $this->view('admin/diagnostics/index', [
            'sessions' => $this->diagnosticModel->getAllSessions(50),
            'stats' => [
                'total_scans' => $this->diagnosticModel->getScanCount(),
                'active_faults' => $this->diagnosticModel->getActiveFaultCount(),
            ],
        ]);
    }

    public function show($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        $session = $this->diagnosticModel->getSession((int) $id);
        if (!$session) {
            error('جلسه تشخیصی یافت نشد.');
            redirect(SITE_URL . '/admin/diagnostics');
        }

        $this->view('admin/diagnostics/show', [
            'session' => $session,
            'results' => $this->diagnosticModel->getSessionResults((int) $id),
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

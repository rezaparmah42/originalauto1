<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Diagnostic;
use App\Models\DiagnosticAI;
use App\Services\AIAnalyzerService;
use App\Models\Vehicle;

class AIRepairController extends Controller
{
    private $diagnosticModel;
    private $aiModel;
    private $analyzer;
    private $vehicleModel;

    public function __construct()
    {
        $this->diagnosticModel = new Diagnostic();
        $this->aiModel = new DiagnosticAI();
        $this->analyzer = new AIAnalyzerService();
        $this->vehicleModel = new Vehicle();
    }

    public function index()
    {
        requireCustomer();

        $vehicles = $this->vehicleModel->getUserVehicles(currentCustomerId());
        $this->view('ai/index', ['vehicles' => $vehicles]);
    }

    public function analyze()
    {
        requireCustomer();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                error('درخواست نامعتبر');
                redirect(SITE_URL . '/ai-diagnostic');
            }

            $vehicleId = (int) ($_POST['vehicle_id'] ?? 0);
            $vehicle = $this->vehicleModel->findById($vehicleId);
            // ensure the vehicle belongs to the current customer
            if (!$vehicle || (int) ($vehicle['user_id'] ?? 0) !== (int) currentCustomerId()) {
                error('دسترسی به خودرو مورد نظر مجاز نیست.');
                redirect(SITE_URL . '/ai-diagnostic');
            }
            $codesRaw = trim((string) ($_POST['codes'] ?? ''));
            $codes = array_values(array_filter(array_map('trim', preg_split('/[\s,;]+/', $codesRaw))));

            $analysis = $this->analyzer->analyzeDTC($codes);
            $report = $this->analyzer->generateReport($vehicleId, $analysis);

            $this->view('ai/analyze', [
                'vehicle' => $vehicle,
                'analysis' => $analysis,
                'report' => $report,
            ]);
            return;
        }

        redirect(SITE_URL . '/ai-diagnostic');
    }

    public function report($vehicleId)
    {
        requireCustomer();

        $vehicleId = (int) $vehicleId;
        $report = null;

        // For now, generate a report based on latest diagnostic results
        $latest = $this->diagnosticModel->getLatestDiagnostic($vehicleId);
        $analysis = [];
        if ($latest) {
            $analysis = $this->analyzer->analyzeDTC([$latest['code'] ?? '']);
            $report = $this->analyzer->generateReport($vehicleId, $analysis);
        }

        $vehicle = $this->vehicleModel->findById($vehicleId);
        if (!$vehicle || (int) ($vehicle['user_id'] ?? 0) !== (int) currentCustomerId()) {
            error('دسترسی به خودرو مورد نظر مجاز نیست.');
            redirect(SITE_URL . '/ai-diagnostic');
        }

        $this->view('ai/report', [
            'vehicle' => $vehicle,
            'analysis' => $analysis,
            'report' => $report,
        ]);
    }

    // API endpoint for programmatic analysis
    public function apiAnalyze()
    {
        if (!isCustomerLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Authentication required.'], JSON_UNESCAPED_UNICODE);
            http_response_code(401);
            exit;
        }

        $_method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (strtoupper($_method) !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            http_response_code(405);
            exit;
        }

        $input = file_get_contents('php://input');
        $data = [];
        if (!empty($input)) {
            $json = json_decode($input, true);
            if (is_array($json)) $data = $json;
        }

        if (empty($data)) {
            $data = $_POST;
        }

        $codes = [];
        if (!empty($data['dtc_codes']) && is_array($data['dtc_codes'])) {
            $codes = $data['dtc_codes'];
        } elseif (!empty($data['dtc_codes']) && is_string($data['dtc_codes'])) {
            $codes = array_values(array_filter(array_map('trim', preg_split('/[\s,;]+/', $data['dtc_codes']))));
        }

        $vehicleId = (int) ($data['vehicle_id'] ?? 0);
        if ($vehicleId <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Vehicle id is required.'], JSON_UNESCAPED_UNICODE);
            http_response_code(422);
            exit;
        }

        $vehicle = $this->vehicleModel->findById($vehicleId);
        if (!$vehicle || (int) ($vehicle['user_id'] ?? 0) !== (int) currentCustomerId()) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Vehicle not found or access denied.'], JSON_UNESCAPED_UNICODE);
            http_response_code(404);
            exit;
        }

        $analysisResult = $this->analyzer->analyzeDTC($codes);
        $report = $this->analyzer->generateReport($vehicleId, $analysisResult);

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'health_score' => $report['health_score'] ?? $analysisResult['health_score'],
            'faults' => $analysisResult['analysis'],
            'recommendations' => $analysisResult['recommendations'],
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

<?php

namespace App\Controllers\API;

use App\Core\Controller;
use App\Core\APIResponse;
use App\Middleware\APIMiddleware;
use App\Models\Diagnostic;
use App\Models\Vehicle;
use App\Services\AIAnalyzerService;

class DiagnosticsController extends Controller
{
    public function index()
    {
        $token = APIMiddleware::protect();
        $diagnosticModel = new Diagnostic();
        $diagnostics = $diagnosticModel->getUserDiagnostics((int) $token['user_id']);

        APIResponse::success(['diagnostics' => $diagnostics]);
    }

    public function analyze()
    {
        $token = APIMiddleware::protect();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            APIResponse::error('Method not allowed', 405);
        }

        $data = APIMiddleware::getRequestData();
        $codes = [];
        if (!empty($data['dtc_codes']) && is_array($data['dtc_codes'])) {
            $codes = $data['dtc_codes'];
        } elseif (!empty($data['dtc_codes']) && is_string($data['dtc_codes'])) {
            $codes = array_values(array_filter(array_map('trim', preg_split('/[\s,;]+/', $data['dtc_codes']))));
        }

        $vehicleId = isset($data['vehicle_id']) ? (int) $data['vehicle_id'] : 0;
        if ($vehicleId > 0) {
            $vehicleModel = new Vehicle();
            $vehicle = $vehicleModel->findById($vehicleId);
            if (!$vehicle || (int) ($vehicle['user_id'] ?? 0) !== (int) $token['user_id']) {
                APIResponse::error('Vehicle not found or access denied.', 404);
            }
        }

        $analyzer = new AIAnalyzerService();
        $analysisResult = $analyzer->analyzeDTC($codes);
        $report = $analyzer->generateReport($vehicleId, $analysisResult);

        APIResponse::success([
            'vehicle_id' => $vehicleId,
            'analysis' => $analysisResult['analysis'],
            'health_score' => $report['health_score'],
            'recommendations' => $report['recommendations'],
            'summary' => $report['summary'],
        ]);
    }

    public function vehicle($id)
    {
        $token = APIMiddleware::protect();
        $vehicleModel = new Vehicle();
        $vehicle = $vehicleModel->getVehicleProfile((int) $id);

        if (!$vehicle || (int) ($vehicle['user_id'] ?? 0) !== (int) $token['user_id']) {
            APIResponse::error('Vehicle not found or access denied.', 404);
        }

        APIResponse::success(['vehicle' => $vehicle]);
    }
}

<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Diagnostic;
use App\Models\OBDCode;
use App\Models\Vehicle;
use App\Services\OBDService;

class DiagnosticController extends Controller
{
    private $vehicleModel;
    private $diagnosticModel;
    private $obdCodeModel;
    private $obdService;

    public function __construct()
    {
        $this->vehicleModel = new Vehicle();
        $this->diagnosticModel = new Diagnostic();
        $this->obdCodeModel = new OBDCode();
        $this->obdService = new OBDService();
    }

    public function index()
    {
        requireCustomer();

        $vehicles = $this->vehicleModel->getUserVehicles(currentCustomerId());
        $this->view('diagnostic/index', [
            'vehicles' => $vehicles,
            'latest' => !empty($vehicles) ? $this->diagnosticModel->getLatestDiagnostic((int) ($vehicles[0]['id'] ?? 0)) : null,
        ]);
    }

    public function connect()
    {
        requireCustomer();

        $vehicleId = (int) ($_GET['vehicle_id'] ?? 0);
        $vehicle = $this->vehicleModel->findById($vehicleId);
        if (!$vehicle || (int) ($vehicle['user_id'] ?? 0) !== (int) currentCustomerId()) {
            redirect(SITE_URL . '/diagnostic');
        }

        $sessionId = $this->diagnosticModel->createSession([
            'vehicle_id' => $vehicleId,
            'user_id' => currentCustomerId(),
            'device_type' => 'ELM327',
            'connection_status' => 'connected',
        ]);

        $this->view('diagnostic/connect', [
            'vehicle' => $vehicle,
            'session_id' => $sessionId,
            'connection' => $this->obdService->connectELM327(),
        ]);
    }

    public function scan()
    {
        requireCustomer();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                error('درخواست نامعتبر است.');
                redirect(SITE_URL . '/diagnostic');
            }

            $vehicleId = (int) ($_POST['vehicle_id'] ?? 0);
            $vehicle = $this->vehicleModel->findById($vehicleId);
            if (!$vehicle || (int) ($vehicle['user_id'] ?? 0) !== (int) currentCustomerId()) {
                redirect(SITE_URL . '/diagnostic');
            }

            $sessionId = $this->diagnosticModel->createSession([
                'vehicle_id' => $vehicleId,
                'user_id' => currentCustomerId(),
                'device_type' => trim((string) ($_POST['device_type'] ?? 'ELM327')),
                'connection_status' => 'connected',
            ]);

            $this->diagnosticModel->updateSessionStatus($sessionId, 'connected');

            $response = $this->obdService->sendCommand('010C');
            $commandResponse = $this->obdService->readResponse($response['response'] ?? 'NO DATA');
            $codes = $this->obdService->parseDTC($commandResponse);

            foreach ($codes as $code) {
                $this->diagnosticModel->saveResult($sessionId, [
                    'error_code' => $code,
                    'raw_data' => $commandResponse,
                    'system' => 'OBD2',
                    'title_fa' => $this->obdCodeModel->findCode($code)['title_fa'] ?? 'کد خطا',
                    'description_fa' => $this->obdCodeModel->findCode($code)['description_fa'] ?? '',
                    'severity' => $this->obdCodeModel->getSeverity($code),
                    'possible_causes' => '',
                    'recommended_actions' => implode("\n", $this->obdCodeModel->getSolutions($code)),
                ]);
            }

            $this->view('diagnostic/scan', [
                'vehicle' => $vehicle,
                'session_id' => $sessionId,
                'results' => $this->diagnosticModel->getSessionResults($sessionId),
                'connection' => $this->obdService->connectELM327(),
                'message' => empty($codes) ? 'از دستگاه کد خطایی دریافت نشد. اتصال، سوئیچ خودرو و کابل OBD را بررسی کنید.' : null,
            ]);
            return;
        }

        $this->view('diagnostic/scan', [
            'vehicle' => null,
            'session_id' => 0,
            'results' => [],
            'connection' => $this->obdService->connectELM327(),
        ]);
    }

    public function result()
    {
        requireCustomer();

        $sessionId = (int) ($_GET['session_id'] ?? 0);
        $session = $this->diagnosticModel->getSession($sessionId);
        if (!$session || (int) ($session['user_id'] ?? 0) !== (int) currentCustomerId()) {
            redirect(SITE_URL . '/diagnostic');
        }

        $this->view('diagnostic/result', [
            'session' => $session,
            'results' => $this->diagnosticModel->getSessionResults($sessionId),
        ]);
    }
}

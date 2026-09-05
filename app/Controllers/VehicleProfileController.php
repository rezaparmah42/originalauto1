<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Maintenance;
use App\Models\Vehicle;

class VehicleProfileController extends Controller
{
    private $vehicleModel;
    private $maintenanceModel;

    public function __construct()
    {
        $this->vehicleModel = new Vehicle();
        $this->maintenanceModel = new Maintenance();
    }

    public function index()
    {
        requireCustomer();

        $vehicleId = $this->resolveVehicleId((int) ($_GET['vehicle_id'] ?? 0));
        if ($vehicleId <= 0) {
            redirect(SITE_URL . '/account/vehicles');
        }

        $vehicle = $this->vehicleModel->findById($vehicleId);
        if (!$vehicle || (int) ($vehicle['user_id'] ?? 0) !== (int) currentCustomerId()) {
            redirect(SITE_URL . '/account/vehicles');
        }

        $fullVehicle = $this->vehicleModel->getVehicleProfile($vehicleId);
        if (!$fullVehicle) {
            redirect(SITE_URL . '/account/vehicles');
        }

        $this->view('account/vehicle-profile/index', [
            'vehicle' => $fullVehicle,
            'upcoming' => $this->maintenanceModel->getUpcomingByVehicle($vehicleId),
            'diagnostics' => $this->maintenanceModel->getDiagnostics($vehicleId),
            'recommendedProducts' => $this->vehicleModel->getVehicleAwareSuggestions($vehicleId, 4),
            'stats' => $this->vehicleModel->getVehicleStats($vehicleId),
        ]);
    }

    public function maintenance()
    {
        requireCustomer();
        $vehicleId = $this->resolveVehicleId((int) ($_GET['vehicle_id'] ?? 0));
        if ($vehicleId <= 0) {
            redirect(SITE_URL . '/account/vehicles');
        }

        $vehicle = $this->vehicleModel->getVehicleProfile($vehicleId);
        if (!$vehicle || (int) ($vehicle['user_id'] ?? 0) !== (int) currentCustomerId()) {
            redirect(SITE_URL . '/account/vehicles');
        }

        $this->view('account/vehicle-profile/maintenance', [
            'vehicle' => $vehicle,
            'records' => $this->maintenanceModel->getByVehicle($vehicleId),
        ]);
    }

    public function history()
    {
        requireCustomer();
        $vehicleId = $this->resolveVehicleId((int) ($_GET['vehicle_id'] ?? 0));
        if ($vehicleId <= 0) {
            redirect(SITE_URL . '/account/vehicles');
        }

        $vehicle = $this->vehicleModel->getVehicleProfile($vehicleId);
        if (!$vehicle || (int) ($vehicle['user_id'] ?? 0) !== (int) currentCustomerId()) {
            redirect(SITE_URL . '/account/vehicles');
        }

        $this->view('account/vehicle-profile/history', [
            'vehicle' => $vehicle,
            'history' => $this->vehicleModel->getVehicleHistory($vehicleId),
        ]);
    }

    public function diagnostics()
    {
        requireCustomer();
        $vehicleId = $this->resolveVehicleId((int) ($_GET['vehicle_id'] ?? 0));
        if ($vehicleId <= 0) {
            redirect(SITE_URL . '/account/vehicles');
        }

        $vehicle = $this->vehicleModel->getVehicleProfile($vehicleId);
        if (!$vehicle || (int) ($vehicle['user_id'] ?? 0) !== (int) currentCustomerId()) {
            redirect(SITE_URL . '/account/vehicles');
        }

        $this->view('account/vehicle-profile/diagnostics', [
            'vehicle' => $vehicle,
            'reports' => $this->maintenanceModel->getDiagnostics($vehicleId),
        ]);
    }

    private function resolveVehicleId(int $vehicleId): int
    {
        if ($vehicleId > 0) {
            return $vehicleId;
        }

        $vehicles = $this->vehicleModel->getUserVehicles(currentCustomerId());
        if (empty($vehicles)) {
            return 0;
        }

        return (int) ($vehicles[0]['id'] ?? 0);
    }
}

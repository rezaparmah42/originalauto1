<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Maintenance;
use App\Models\Vehicle;

class AdminVehicleController extends Controller
{
    private $vehicleModel;
    private $maintenanceModel;

    public function __construct()
    {
        $this->vehicleModel = new Vehicle();
        $this->maintenanceModel = new Maintenance();
    }

    public function intelligence()
    {
        requireLogin();
        $this->requireAdminAccess();

        $vehicles = $this->vehicleModel->getAllVehicles();
        $this->view('admin/vehicles/intelligence', ['vehicles' => $vehicles]);
    }

    public function profile($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        $vehicle = $this->vehicleModel->getVehicleProfile((int) $id);
        if (!$vehicle) {
            error('خودرو مورد نظر یافت نشد.');
            redirect(SITE_URL . '/admin/vehicles/intelligence');
        }

        $this->view('admin/vehicles/profile', [
            'vehicle' => $vehicle,
            'records' => $this->maintenanceModel->getByVehicle((int) $id),
            'diagnostics' => $this->maintenanceModel->getDiagnostics((int) $id),
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

<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Maintenance;
use App\Models\Vehicle;

class MaintenanceController extends Controller
{
    private $maintenanceModel;
    private $vehicleModel;

    public function __construct()
    {
        $this->maintenanceModel = new Maintenance();
        $this->vehicleModel = new Vehicle();
    }

    public function create()
    {
        requireCustomer();

        $vehicleId = $this->resolveVehicleId((int) ($_POST['vehicle_id'] ?? 0));
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                error('درخواست نامعتبر است.');
                redirect($this->profileRedirect($vehicleId));
            }

            $vehicle = $this->vehicleModel->findById($vehicleId);
            if (!$vehicle || (int) ($vehicle['user_id'] ?? 0) !== (int) currentCustomerId()) {
                redirect(SITE_URL . '/account/vehicles');
            }

            $this->maintenanceModel->createReminder([
                'vehicle_id' => $vehicleId,
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'service_date' => trim($_POST['service_date'] ?? date('Y-m-d')),
                'next_service_date' => trim($_POST['next_service_date'] ?? $this->maintenanceModel->calculateNextServiceDate($_POST['service_date'] ?? date('Y-m-d'), (int) ($vehicle['mileage'] ?? 0))),
                'mileage' => (int) ($vehicle['mileage'] ?? 0),
                'status' => 'scheduled',
            ]);

            success('یادآوری تعمیر و نگهداری ثبت شد.');
        }

        redirect($this->profileRedirect($vehicleId));
    }

    public function updateStatus($id)
    {
        requireCustomer();

        $vehicleId = $this->resolveVehicleId((int) ($_POST['vehicle_id'] ?? 0));
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
            $status = trim($_POST['status'] ?? 'scheduled');
            $this->maintenanceModel->updateStatus((int) $id, $status);
            success('وضعیت به‌روزرسانی شد.');
        }

        redirect($this->profileRedirect($vehicleId));
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

    private function profileRedirect(int $vehicleId): string
    {
        if ($vehicleId > 0) {
            return SITE_URL . '/account/vehicle-profile?vehicle_id=' . $vehicleId;
        }

        return SITE_URL . '/account/vehicle-profile';
    }
}

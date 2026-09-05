<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    private $vehicleModel;

    public function __construct()
    {
        $this->vehicleModel = new Vehicle();
    }

    public function index()
    {
        if (isCustomerLoggedIn()) {
            requireCustomer();
            $vehicles = $this->vehicleModel->getUserVehicles(currentCustomerId());
            $this->view('account/vehicles/index', ['vehicles' => $vehicles]);
            return;
        }

        $this->view('vehicles/index');
    }

    public function create()
    {
        requireCustomer();
        $brands = $this->vehicleModel->getBrands();
        $this->view('account/vehicles/create', ['brands' => $brands, 'vehicle' => []]);
    }

    public function store()
    {
        requireCustomer();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/account/vehicles');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            $this->view('account/vehicles/create', ['brands' => $this->vehicleModel->getBrands(), 'vehicle' => $_POST]);
            return;
        }

        $data = [
            'brand_id' => trim($_POST['brand_id'] ?? ''),
            'model' => trim($_POST['model'] ?? ''),
            'year' => trim($_POST['year'] ?? ''),
            'engine' => trim($_POST['engine'] ?? ''),
            'vin' => trim($_POST['vin'] ?? ''),
            'mileage' => trim($_POST['mileage'] ?? ''),
        ];

        $errors = $this->vehicleModel->validateVehicleData($data);
        if (!empty($errors)) {
            $this->view('account/vehicles/create', ['brands' => $this->vehicleModel->getBrands(), 'vehicle' => $data, 'errors' => $errors]);
            return;
        }

        $result = $this->vehicleModel->createVehicle([
            'user_id' => currentCustomerId(),
            'brand_id' => $data['brand_id'],
            'model' => $data['model'],
            'year' => $data['year'],
            'engine' => $data['engine'],
            'vin' => $data['vin'],
            'mileage' => $data['mileage'],
        ]);

        if ($result) {
            success('خودرو با موفقیت ثبت شد.');
            redirect(SITE_URL . '/account/vehicles');
        }

        error('ثبت خودرو با خطا مواجه شد.');
        $this->view('account/vehicles/create', ['brands' => $this->vehicleModel->getBrands(), 'vehicle' => $data]);
    }

    public function edit($id)
    {
        requireCustomer();

        $vehicle = $this->vehicleModel->findById((int) $id);
        if (!$vehicle || (int) $vehicle['user_id'] !== (int) currentCustomerId()) {
            error('خودرو مورد نظر پیدا نشد.');
            redirect(SITE_URL . '/account/vehicles');
        }

        $this->view('account/vehicles/edit', ['vehicle' => $vehicle, 'brands' => $this->vehicleModel->getBrands()]);
    }

    public function update($id)
    {
        requireCustomer();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/account/vehicles/edit/' . (int) $id);
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/account/vehicles/edit/' . (int) $id);
        }

        $vehicle = $this->vehicleModel->findById((int) $id);
        if (!$vehicle || (int) $vehicle['user_id'] !== (int) currentCustomerId()) {
            error('خودرو مورد نظر پیدا نشد.');
            redirect(SITE_URL . '/account/vehicles');
        }

        $data = [
            'brand_id' => trim($_POST['brand_id'] ?? ''),
            'model' => trim($_POST['model'] ?? ''),
            'year' => trim($_POST['year'] ?? ''),
            'engine' => trim($_POST['engine'] ?? ''),
            'vin' => trim($_POST['vin'] ?? ''),
            'mileage' => trim($_POST['mileage'] ?? ''),
        ];

        $errors = $this->vehicleModel->validateVehicleData($data);
        if (!empty($errors)) {
            $this->view('account/vehicles/edit', ['vehicle' => array_merge($vehicle, $data), 'brands' => $this->vehicleModel->getBrands(), 'errors' => $errors]);
            return;
        }

        if ($this->vehicleModel->updateVehicle((int) $id, $data)) {
            success('اطلاعات خودرو به‌روزرسانی شد.');
            redirect(SITE_URL . '/account/vehicles');
        }

        error('به‌روزرسانی خودرو با خطا مواجه شد.');
        $this->view('account/vehicles/edit', ['vehicle' => array_merge($vehicle, $data), 'brands' => $this->vehicleModel->getBrands()]);
    }

    public function delete($id)
    {
        requireCustomer();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/account/vehicles');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/account/vehicles');
        }

        $vehicle = $this->vehicleModel->findById((int) $id);
        if (!$vehicle || (int) $vehicle['user_id'] !== (int) currentCustomerId()) {
            error('خودرو مورد نظر پیدا نشد.');
            redirect(SITE_URL . '/account/vehicles');
        }

        if ($this->vehicleModel->deleteVehicle((int) $id)) {
            success('خودرو حذف شد.');
        } else {
            error('حذف خودرو با خطا مواجه شد.');
        }

        redirect(SITE_URL . '/account/vehicles');
    }

    public function history($id)
    {
        requireCustomer();

        $vehicle = $this->vehicleModel->findById((int) $id);
        if (!$vehicle || (int) $vehicle['user_id'] !== (int) currentCustomerId()) {
            error('خودرو مورد نظر پیدا نشد.');
            redirect(SITE_URL . '/account/vehicles');
        }

        $history = $this->vehicleModel->getVehicleHistory((int) $id);

        $this->view('account/vehicles/history', ['vehicle' => $vehicle, 'history' => $history]);
    }

    public function detail($id)
    {
        requireCustomer();

        $vehicle = $this->vehicleModel->findById((int) $id);
        if (!$vehicle || (int) $vehicle['user_id'] !== (int) currentCustomerId()) {
            error('خودرو مورد نظر پیدا نشد.');
            redirect(SITE_URL . '/account/vehicles');
        }

        $repairModel = new \App\Models\Repair();
        $history = $this->vehicleModel->getVehicleHistory((int) $id);
        $repairParts = [];
        foreach ($history as $entry) {
            if (!empty($entry['repair_id'])) {
                $repairParts[$entry['repair_id']] = $repairModel->getRepairPartHistory((int) $entry['repair_id']);
            }
        }

        $this->view('account/vehicles/detail', [
            'vehicle' => $vehicle,
            'history' => $history,
            'repairParts' => $repairParts,
            'recommendedProducts' => $this->vehicleModel->getVehicleAwareSuggestions((int) $id, 4),
        ]);
    }

    public function adminIndex()
    {
        requireLogin();
        $this->requireAdminAccess();

        $vehicles = $this->vehicleModel->getAllVehicles();
        $this->view('admin/vehicles/index', ['vehicles' => $vehicles]);
    }

    public function adminShow($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        $vehicle = $this->vehicleModel->findById((int) $id);
        if (!$vehicle) {
            error('خودرو مورد نظر یافت نشد.');
            redirect(SITE_URL . '/admin/vehicles');
        }

        $history = $this->vehicleModel->getVehicleHistory((int) $id);
        $this->view('admin/vehicles/show', ['vehicle' => $vehicle, 'history' => $history]);
    }

    private function requireAdminAccess()
    {
        if (!isAdmin()) {
            error('دسترسی مدیریتی لازم است.');
            redirect(SITE_URL . '/admin/login');
        }
    }
}

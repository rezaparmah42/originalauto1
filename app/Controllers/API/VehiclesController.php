<?php

namespace App\Controllers\API;

use App\Core\Controller;
use App\Core\APIResponse;
use App\Middleware\APIMiddleware;
use App\Models\Vehicle;

class VehiclesController extends Controller
{
    public function index()
    {
        $token = APIMiddleware::protect();
        $vehicleModel = new Vehicle();
        $vehicles = $vehicleModel->getUserVehicles((int) $token['user_id']);

        APIResponse::success(['vehicles' => $vehicles]);
    }

    public function show($id = null)
    {
        $token = APIMiddleware::protect();
        $vehicleModel = new Vehicle();
        $vehicle = $vehicleModel->getVehicleProfile((int) $id);

        if (!$vehicle || (int) ($vehicle['user_id'] ?? 0) !== (int) $token['user_id']) {
            APIResponse::error('Vehicle not found or access denied.', 404);
        }

        APIResponse::success(['vehicle' => $vehicle]);
    }

    public function store()
    {
        $token = APIMiddleware::protect();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            APIResponse::error('Method not allowed', 405);
        }

        $data = APIMiddleware::getRequestData();
        $payload = [
            'brand_id' => $data['brand_id'] ?? '',
            'model' => trim((string) ($data['model'] ?? '')),
            'year' => trim((string) ($data['year'] ?? '')),
            'engine' => trim((string) ($data['engine'] ?? '')),
            'vin' => trim((string) ($data['vin'] ?? '')),
            'mileage' => $data['mileage'] ?? '',
        ];

        $vehicleModel = new Vehicle();
        $errors = $vehicleModel->validateVehicleData($payload);
        if (!empty($errors)) {
            APIResponse::error('Validation failed.', 422, ['errors' => $errors]);
        }

        $vehicleId = $vehicleModel->createVehicle([
            'user_id' => (int) $token['user_id'],
            'brand_id' => $payload['brand_id'],
            'model' => $payload['model'],
            'year' => $payload['year'],
            'engine' => $payload['engine'],
            'vin' => $payload['vin'],
            'mileage' => $payload['mileage'],
        ]);

        if (!$vehicleId) {
            APIResponse::error('Unable to create vehicle.', 500);
        }

        APIResponse::success(['vehicle_id' => (int) $vehicleId]);
    }
}

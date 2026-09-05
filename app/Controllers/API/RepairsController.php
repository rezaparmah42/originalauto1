<?php

namespace App\Controllers\API;

use App\Core\Controller;
use App\Core\APIResponse;
use App\Middleware\APIMiddleware;
use App\Models\Repair;

class RepairsController extends Controller
{
    public function index()
    {
        $token = APIMiddleware::protect();
        $repairModel = new Repair();
        $repairs = $repairModel->getCustomerRepairs((int) $token['user_id']);

        APIResponse::success(['repairs' => $repairs]);
    }

    public function show($id = null)
    {
        $token = APIMiddleware::protect();
        $repairModel = new Repair();
        $repair = $repairModel->findById((int) $id);

        if (!$repair) {
            APIResponse::error('Repair not found.', 404);
        }

        $customerCheck = $repairModel->isRepairForUser((int) $id, (int) $token['user_id']);
        if (!$customerCheck) {
            APIResponse::error('Repair access denied.', 403);
        }

        APIResponse::success(['repair' => $repair]);
    }
}

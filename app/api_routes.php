<?php

use App\Controllers\API\AuthController;
use App\Controllers\API\DiagnosticsController;
use App\Controllers\API\NotificationsController;
use App\Controllers\API\OrdersController;
use App\Controllers\API\PaymentsController;
use App\Controllers\API\RepairsController;
use App\Controllers\API\VehiclesController;

$router->post('/api/auth/login', [AuthController::class, 'login']);
$router->post('/api/auth/register', [AuthController::class, 'register']);
$router->post('/api/auth/logout', [AuthController::class, 'logout']);
$router->get('/api/auth/me', [AuthController::class, 'me']);

$router->get('/api/vehicles', [VehiclesController::class, 'index']);
$router->post('/api/vehicles', [VehiclesController::class, 'store']);
$router->get('/api/vehicles/{id}', [VehiclesController::class, 'show']);

$router->get('/api/diagnostics', [DiagnosticsController::class, 'index']);
$router->post('/api/diagnostics/analyze', [DiagnosticsController::class, 'analyze']);
$router->get('/api/diagnostics/vehicle/{id}', [DiagnosticsController::class, 'vehicle']);

$router->get('/api/repairs', [RepairsController::class, 'index']);
$router->get('/api/repairs/{id}', [RepairsController::class, 'show']);

$router->get('/api/orders', [OrdersController::class, 'index']);
$router->get('/api/orders/{id}', [OrdersController::class, 'show']);
$router->post('/api/orders/place', [OrdersController::class, 'place']);

$router->get('/api/payments/{id}', [PaymentsController::class, 'show']);

$router->get('/api/notifications', [NotificationsController::class, 'index']);
$router->post('/api/notifications/read', [NotificationsController::class, 'markRead']);

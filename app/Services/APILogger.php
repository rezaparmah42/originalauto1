<?php

namespace App\Services;

use App\Models\ApiLog;
use App\Models\ApiToken;

class APILogger
{
    public function logRequest(string $endpoint, string $method, int $responseCode, ?int $userId = null)
    {
        $ipAddress = $this->getIpAddress();
        $apiLog = new ApiLog();
        $apiLog->create([
            'user_id' => $userId,
            'endpoint' => $endpoint,
            'method' => $method,
            'ip_address' => $ipAddress,
            'response_code' => $responseCode,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getIpAddress(): string
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($parts[0]);
        }

        if (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return '0.0.0.0';
    }
}

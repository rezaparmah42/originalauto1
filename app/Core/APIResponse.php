<?php

namespace App\Core;

use App\Services\APILogger;
use App\Middleware\APIMiddleware;

class APIResponse
{
    public static function json(array $data, int $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        $response = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($response === false) {
            $response = json_encode(['status' => 'error', 'message' => 'Unable to encode response'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            http_response_code(500);
        }

        self::logApiResponse($statusCode);
        echo $response;
        exit;
    }

    private static function logApiResponse(int $statusCode): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        if (strpos($uri, '/api/') !== 0) {
            return;
        }

        try {
            $logger = new APILogger();
            $token = null;
            try {
                $token = APIMiddleware::getBearerToken();
            } catch (\Throwable $e) {
                $token = null;
            }

            $userId = null;
            if ($token !== null) {
                try {
                    $tokenModel = new \App\Models\ApiToken();
                    $tokenData = $tokenModel->findByToken($token);
                    $userId = $tokenData['user_id'] ?? null;
                } catch (\Throwable $e) {
                    // ignore token lookup failures
                    $userId = null;
                }
            }

            $logger->logRequest($uri, $_SERVER['REQUEST_METHOD'] ?? 'GET', $statusCode, $userId);
        } catch (\Throwable $e) {
            // swallow logging errors to avoid breaking API responses
        }
    }

    public static function error(string $message, int $statusCode = 400, array $extra = [])
    {
        self::json(array_merge(['status' => 'error', 'message' => $message], $extra), $statusCode);
    }

    public static function success(array $data = [], int $statusCode = 200)
    {
        self::json(array_merge(['status' => 'ok'], $data), $statusCode);
    }
}

<?php

namespace App\Middleware;

use App\Core\APIResponse;
use App\Models\ApiDevice;
use App\Models\ApiToken;

class APIMiddleware
{
    public static function getRequestData(): array
    {
        $input = file_get_contents('php://input');
        $data = [];

        if (!empty($input)) {
            $json = json_decode($input, true);
            if (is_array($json)) {
                $data = $json;
            }
        }

        if (empty($data) && !empty($_POST)) {
            $data = $_POST;
        }

        return $data;
    }

    public static function getAuthorizationHeader(): ?string
    {
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            return trim($_SERVER['HTTP_AUTHORIZATION']);
        }

        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            foreach ($headers as $key => $value) {
                if (strtolower($key) === 'authorization') {
                    return trim($value);
                }
            }
        }

        return null;
    }

    public static function getBearerToken(): ?string
    {
        $header = self::getAuthorizationHeader();
        if ($header !== null && preg_match('/^Bearer\s+(.*)$/i', $header, $matches)) {
            return trim($matches[1]);
        }

        if (!empty($_GET['token'])) {
            return trim((string) $_GET['token']);
        }

        return null;
    }

    public static function authenticate(): array
    {
        $tokenValue = self::getBearerToken();
        if ($tokenValue === null) {
            APIResponse::error('Authorization token missing.', 401);
        }

        $tokenModel = new ApiToken();
        $token = $tokenModel->findByToken($tokenValue);
        if (empty($token)) {
            APIResponse::error('Invalid API token.', 401);
        }

        if (!empty($token['revoked']) && (int) $token['revoked'] === 1) {
            APIResponse::error('API token has been revoked.', 401);
        }

        if (!empty($token['expires_at']) && strtotime($token['expires_at']) < time()) {
            APIResponse::error('API token has expired.', 401);
        }

        $tokenModel->touchLastUsed($tokenValue);

        try {
            $deviceModel = new ApiDevice();
            $device = $deviceModel->findByTokenId((int) ($token['id'] ?? 0));
            if (!empty($device)) {
                $deviceModel->updateLastActive((int) $device['id']);
            }
        } catch (\Throwable $e) {
            // ignore device update failures
        }

        return $token;
    }

    public static function protect(): array
    {
        return self::authenticate();
    }
}

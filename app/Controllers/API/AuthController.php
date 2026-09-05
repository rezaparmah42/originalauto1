<?php

namespace App\Controllers\API;

use App\Core\Controller;
use App\Core\APIResponse;
use App\Middleware\APIMiddleware;
use App\Models\ApiToken;
use App\Models\User;

class AuthController extends Controller
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            APIResponse::error('Method not allowed', 405);
        }

        $data = APIMiddleware::getRequestData();
        $login = trim((string) ($data['login'] ?? $data['phone'] ?? ''));
        $password = $data['password'] ?? '';

        if ($login === '' || $password === '') {
            APIResponse::error('Phone/email and password are required.', 422);
        }

        $userModel = new User();
        $user = $userModel->findByPhoneOrEmail($login);
        if (!$user || !password_verify($password, $user['password'] ?? '')) {
            APIResponse::error('Invalid credentials.', 401);
        }

        $tokenModel = new ApiToken();
        $token = $tokenModel->createToken((int) $user['id'], 'mobile-app-token');
        if (!$token) {
            APIResponse::error('Unable to create API token.', 500);
        }

        APIResponse::success([
            'token' => $token,
            'user' => [
                'id' => (int) $user['id'],
                'name' => $user['name'],
                'phone' => $user['phone'],
                'email' => $user['email'],
                'role' => $user['role'],
            ],
        ]);
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            APIResponse::error('Method not allowed', 405);
        }

        $data = APIMiddleware::getRequestData();
        $name = trim((string) ($data['name'] ?? ''));
        $phone = trim((string) ($data['phone'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $password = $data['password'] ?? '';
        $confirm = $data['password_confirm'] ?? $data['confirm_password'] ?? '';

        if ($name === '' || $phone === '' || $password === '' || $confirm === '') {
            APIResponse::error('Name, phone, password, and confirmation are required.', 422);
        }

        if ($password !== $confirm) {
            APIResponse::error('Password confirmation does not match.', 422);
        }

        if (!isMobile($phone)) {
            APIResponse::error('Invalid mobile phone number.', 422);
        }

        if ($email !== '' && !isEmail($email)) {
            APIResponse::error('Invalid email address.', 422);
        }

        $userModel = new User();
        if ($userModel->findByPhone($phone)) {
            APIResponse::error('Phone number already registered.', 409);
        }

        if ($email !== '' && $userModel->findByEmail($email)) {
            APIResponse::error('Email address already registered.', 409);
        }

        $userCreated = $userModel->createUser([
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        if (!$userCreated) {
            APIResponse::error('Unable to register user.', 500);
        }

        $user = $userModel->findByPhone($phone);
        $tokenModel = new ApiToken();
        $token = $tokenModel->createToken((int) $user['id'], 'mobile-app-token');

        if (!$token) {
            APIResponse::error('Unable to create API token.', 500);
        }

        APIResponse::success([
            'token' => $token,
            'user' => [
                'id' => (int) $user['id'],
                'name' => $user['name'],
                'phone' => $user['phone'],
                'email' => $user['email'],
                'role' => $user['role'],
            ],
        ]);
    }

    public function logout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            APIResponse::error('Method not allowed', 405);
        }

        $tokenValue = APIMiddleware::getBearerToken();
        if ($tokenValue === null) {
            APIResponse::error('Authorization token missing.', 401);
        }

        $tokenModel = new ApiToken();
        $token = $tokenModel->findByToken($tokenValue);
        if (!$token) {
            APIResponse::error('Invalid API token.', 401);
        }

        $tokenModel->revokeToken($tokenValue);
        APIResponse::success(['message' => 'Logged out successfully']);
    }

    public function me()
    {
        $token = APIMiddleware::protect();
        APIResponse::success([
            'user' => [
                'id' => (int) $token['user_id'],
                'name' => $token['user_name'],
                'phone' => $token['user_phone'],
                'email' => $token['user_email'],
                'role' => $token['user_role'],
            ],
            'token' => [
                'expires_at' => $token['expires_at'],
                'last_used_at' => $token['last_used_at'],
            ],
        ]);
    }
}

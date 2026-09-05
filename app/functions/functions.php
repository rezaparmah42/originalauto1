<?php

/**
 * ======================================================
 * Original Shargh CMS
 * Global Helper Functions
 * Part 1
 * ======================================================
 */

if (!function_exists('e')) {

    function e($text)
    {
        return htmlspecialchars(
            $text ?? '',
            ENT_QUOTES,
            'UTF-8'
        );
    }

}

if (!function_exists('isInternalUrl')) {

    function isInternalUrl($url)
    {
        $url = trim((string)$url);
        if ($url === '') {
            return false;
        }

        $url = filter_var($url, FILTER_SANITIZE_URL);
        if ($url === false) {
            return false;
        }

        $parsed = parse_url($url);
        if (empty($parsed['host'])) {
            return true;
        }

        $siteHost = parse_url(SITE_URL, PHP_URL_HOST);
        return $siteHost !== null && strtolower($parsed['host']) === strtolower($siteHost);
    }

}

if (!function_exists('redirect')) {

    function redirect($url)
    {
        $url = trim((string)$url);
        if (!isInternalUrl($url)) {
            $url = SITE_URL;
        }

        $url = filter_var($url, FILTER_SANITIZE_URL);
        header("Location: {$url}");
        exit;
    }

}

if (!function_exists('back')) {

    function back()
    {
        $url = $_SERVER['HTTP_REFERER'] ?? SITE_URL;
        if (!isInternalUrl($url)) {
            $url = SITE_URL;
        }
        redirect($url);
    }

}

if (!function_exists('isPost')) {

    function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

}

if (!function_exists('isGet')) {

    function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

}

if (!function_exists('old')) {

    function old($key, $default = '')
    {
        return $_POST[$key] ?? $default;
    }

}

if (!function_exists('asset')) {

    function asset($path)
    {
        return SITE_URL . '/assets/' . ltrim($path, '/');
    }

}

if (!function_exists('upload')) {

    function upload($path)
    {
        return UPLOAD_URL . ltrim($path, '/');
    }

}

if (!function_exists('currentDate')) {

    function currentDate()
    {
        return date('Y-m-d H:i:s');
    }

}

if (!function_exists('secureRandomBytes')) {

    function secureRandomBytes($length)
    {
        if (function_exists('random_bytes')) {
            return random_bytes($length);
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            return openssl_random_pseudo_bytes($length);
        }

        $result = '';
        while (strlen($result) < $length) {
            $result .= md5(uniqid(mt_rand(), true));
        }

        return substr($result, 0, $length);
    }

}

if (!function_exists('randomString')) {

    function randomString($length = 20)
    {
        return bin2hex(secureRandomBytes((int) ceil($length / 2)));
    }

}
/*
|--------------------------------------------------------------------------
| Session Helpers
|--------------------------------------------------------------------------
*/

if (!function_exists('setSession')) {

    function setSession($key, $value)
    {
        ensureSessionStarted();
        $_SESSION[$key] = $value;
    }

}

if (!function_exists('getSession')) {

    function getSession($key, $default = null)
    {
        ensureSessionStarted();
        return $_SESSION[$key] ?? $default;
    }

}

if (!function_exists('removeSession')) {

    function removeSession($key)
    {
        ensureSessionStarted();
        unset($_SESSION[$key]);
    }

}

if (!function_exists('ensureSessionStarted')) {

    function ensureSessionStarted()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

}

if (!function_exists('isCustomerLoggedIn')) {

    function isCustomerLoggedIn()
    {
        ensureSessionStarted();
        return isset($_SESSION['customer_id']);
    }

}

if (!function_exists('currentCustomerId')) {

    function currentCustomerId()
    {
        ensureSessionStarted();
        return $_SESSION['customer_id'] ?? null;
    }

}

if (!function_exists('currentCustomerName')) {

    function currentCustomerName()
    {
        ensureSessionStarted();
        return $_SESSION['customer_name'] ?? 'کاربر';
    }

}

if (!function_exists('requireCustomer')) {

    function requireCustomer()
    {
        ensureSessionStarted();
        if (!isCustomerLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? SITE_URL;
            redirect(SITE_URL . '/login');
        }
    }

}

if (!function_exists('isAdmin')) {

    function isAdmin()
    {
        ensureSessionStarted();
        if (!isset($_SESSION['admin'])) {
            return false;
        }

        $role = strtolower((string) ($_SESSION['admin']['role'] ?? ''));
        return in_array($role, ['admin', 'manager', 'administrator', 'superadmin', 'owner'], true);
    }

}

if (!function_exists('isLoggedIn')) {

    function isLoggedIn()
    {
        ensureSessionStarted();
        if (isAdmin()) {
            return true;
        }

        return isCustomerLoggedIn();
    }

}

if (!function_exists('requireLogin')) {

    function requireLogin()
    {
        ensureSessionStarted();

        if (isAdmin() || isCustomerLoggedIn()) {
            return;
        }

        $requestUri = strtolower((string) ($_SERVER['REQUEST_URI'] ?? ''));
        if ($requestUri !== '' && strpos($requestUri, '/admin') === 0) {
            redirect(SITE_URL . '/admin/login');
        }

        redirect(SITE_URL . '/login');
    }

}

/*
|--------------------------------------------------------------------------
| Flash Messages
|--------------------------------------------------------------------------
*/

if (!function_exists('success')) {

    function success($message)
    {
        ensureSessionStarted();
        $_SESSION['success'] = $message;
    }

}

if (!function_exists('error')) {

    function error($message)
    {
        ensureSessionStarted();
        $_SESSION['error'] = $message;
    }

}

if (!function_exists('flash')) {

    function flash($key)
    {
        ensureSessionStarted();
        if (!isset($_SESSION[$key])) {
            return null;
        }

        $message = $_SESSION[$key];

        unset($_SESSION[$key]);

        return $message;
    }

}

/*
|--------------------------------------------------------------------------
| CSRF Token
|--------------------------------------------------------------------------
*/

if (!function_exists('csrf_token')) {

    function csrf_token()
    {
        ensureSessionStarted();
        if (empty($_SESSION['_token'])) {

            $_SESSION['_token'] = bin2hex(secureRandomBytes(32));

        }

        return $_SESSION['_token'];
    }

}

if (!function_exists('csrf_field')) {

    function csrf_field()
    {
        return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
    }

}

if (!function_exists('verify_csrf')) {

    function verify_csrf()
    {
        ensureSessionStarted();

        if (!isset($_POST['_token'])) {

            return false;

        }

        return hash_equals(

            $_SESSION['_token'] ?? '',

            $_POST['_token']

        );

    }

}
/*
|--------------------------------------------------------------------------
| Validation Helpers
|--------------------------------------------------------------------------
*/

if (!function_exists('isEmail')) {

    function isEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

}

if (!function_exists('isMobile')) {

    function isMobile($mobile)
    {
        return preg_match('/^09[0-9]{9}$/', $mobile) === 1;
    }

}

if (!function_exists('required')) {

    function required($value)
    {
        return trim((string)$value) !== '';
    }

}

if (!function_exists('minLength')) {

    function minLength($value, $length)
    {
        return mb_strlen(trim((string)$value)) >= $length;
    }

}

if (!function_exists('maxLength')) {

    function maxLength($value, $length = 0)
    {
        return mb_strlen(trim((string)$value)) <= $length;
    }

}

/*
|--------------------------------------------------------------------------
| Text Helpers
|--------------------------------------------------------------------------
*/

if (!function_exists('slug')) {

    function slug($text)
    {
        $text = trim($text);

        $text = preg_replace('/[^A-Za-z0-9آ-ی\s-]/u', '', $text);

        $text = preg_replace('/[\s-]+/', '-', $text);

        return strtolower($text);
    }

}

if (!function_exists('limit')) {

    function limit($text, $length = 120)
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length) . '...';
    }

}

/*
|--------------------------------------------------------------------------
| Date & Price Helpers
|--------------------------------------------------------------------------
*/

if (!function_exists('formatDate')) {

    function formatDate($date)
    {
        if (!$date) {
            return '-';
        }

        return date('Y/m/d', strtotime($date));
    }

}

if (!function_exists('price')) {

    function price($number)
    {
        return number_format((float)$number) . ' تومان';
    }

}
/*
|--------------------------------------------------------------------------
| Upload Helpers
|--------------------------------------------------------------------------
*/

if (!function_exists('generateFileName')) {

    function generateFileName($extension)
    {
        return uniqid(date('YmdHis') . '_', true) . '.' . strtolower($extension);
    }

}

if (!function_exists('deleteFile')) {

    function deleteFile($file)
    {
        if (file_exists($file)) {
            return unlink($file);
        }

        return false;
    }

}

if (!function_exists('ensureUploadPath')) {

    function ensureUploadPath()
    {
        if (!defined('UPLOAD_PATH')) {
            return null;
        }

        $uploadPath = rtrim(UPLOAD_PATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!is_dir($uploadPath)) {
            if (!mkdir($uploadPath, 0755, true) && !is_dir($uploadPath)) {
                return null;
            }
        }

        return $uploadPath;
    }

}

if (!function_exists('upload_file')) {

    function upload_file(array $file, array $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'], int $maxSize = 5242880)
    {
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return null;
        }

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }

        if (($file['size'] ?? 0) > $maxSize || ($file['size'] ?? 0) <= 0) {
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            return null;
        }

        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if ($mimeType === false || !in_array($mimeType, $allowedMimeTypes, true)) {
            return null;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $extension = preg_replace('/[^a-z0-9]/', '', $extension);
        $extension = $extension !== '' ? $extension : 'bin';

        $filename = generateFileName($extension);
        $destination = ensureUploadPath();

        if ($destination === null) {
            return null;
        }

        $destination .= $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return null;
        }

        return $filename;
    }

}

/*
|--------------------------------------------------------------------------
| Settings Helper
|--------------------------------------------------------------------------
*/

if (!function_exists('setting')) {

    function setting($key, $default = null)
    {
        static $settings = [];

        if (!array_key_exists($key, $settings)) {

            try {

                // Ensure Database class is available even if autoloader isn't registered
                if (!class_exists('\\App\\Core\\Database')) {
                    $dbFile = __DIR__ . '/../Core/Database.php';
                    if (file_exists($dbFile)) {
                        require_once $dbFile;
                    }
                }

                // Prefer fully-qualified class to avoid relying on external autoloaders
                $db = \App\Core\Database::connect();

                $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
                $stmt->execute([$key]);

                $result = $stmt->fetch();
                $settings[$key] = $result ? $result['setting_value'] : $default;

            } catch (Exception $e) {

                $settings[$key] = $default;

            }

        }

        return $settings[$key];
    }

}

/*
|--------------------------------------------------------------------------
| Debug Helper
|--------------------------------------------------------------------------
*/

if (!function_exists('dd')) {

    function dd($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        exit;
    }

}

/*
|--------------------------------------------------------------------------
| JSON Response
|--------------------------------------------------------------------------
*/

if (!function_exists('json')) {

    function json($data, $status = 200)
    {
        http_response_code($status);

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($data, JSON_UNESCAPED_UNICODE);

        exit;
    }

}
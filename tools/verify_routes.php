<?php
require_once __DIR__ . '/../config/config.php';

spl_autoload_register(function ($class) {
    $class = str_replace('App\\', '', $class);
    $class = str_replace('\\', '/', $class);
    $file = __DIR__ . '/../app/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

class DummyRouter {
    public $routes = ['GET' => [], 'POST' => []];
    public function get($path, $action) { $this->routes['GET'][$path] = $action; }
    public function post($path, $action) { $this->routes['POST'][$path] = $action; }
}

$router = new DummyRouter();

$files = [__DIR__ . '/../app/routes.php', __DIR__ . '/../app/api_routes.php'];
foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "Missing route file: $file\n";
        continue;
    }
    include $file;
}

$errors = [];
foreach ($router->routes as $method => $routes) {
    foreach ($routes as $path => $action) {
        if (is_array($action)) {
            [$class, $methodName] = $action;
            if (!class_exists($class)) {
                $errors[] = "$method $path -> missing class $class";
                continue;
            }
            if (!method_exists($class, $methodName)) {
                $errors[] = "$method $path -> missing method $class::$methodName";
            }
        } elseif (is_string($action)) {
            if (function_exists($action)) continue;
            $errors[] = "$method $path -> unsupported action type string action";
        } elseif (is_callable($action)) {
            // closure, ok
        } else {
            $errors[] = "$method $path -> unsupported action type";
        }
    }
}

if (empty($errors)) {
    echo "ROUTES OK\n";
    exit(0);
}
foreach ($errors as $err) {
    echo $err . "\n";
}
exit(1);

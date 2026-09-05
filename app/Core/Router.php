<?php

namespace App\Core;

class Router
{
    private $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get($path, $action)
    {
        $this->routes['GET'][$path] = $action;
    }

    public function post($path, $action)
    {
        $this->routes['POST'][$path] = $action;
    }

    public function run()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $base = dirname($_SERVER['SCRIPT_NAME']);
        if($base !== '/'){
            $uri = str_replace($base, '', $uri);
        }

        $uri = rtrim($uri, '/') ?: '/';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $routes = $this->routes[$method] ?? [];
        if ($method === 'HEAD' && empty($routes)) {
            $routes = $this->routes['GET'] ?? [];
        }

        if(isset($routes[$uri])){
            $action = $routes[$uri];
            $this->dispatch($action);
            return;
        }

        foreach ($routes as $route => $action) {
            if (strpos($route, '{') === false) {
                continue;
            }

            $pattern = preg_replace_callback(
                '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
                function ($matches) {
                    return '(?P<' . $matches[1] . '>[^/]+)';
                },
                $route
            );

            $pattern = '#^' . $pattern . '$#u';
            $matches = [];
            if (preg_match($pattern, $uri, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (!is_int($key)) {
                        $params[$key] = $value;
                    }
                }

                // Decode percent-encoded URI components so controllers receive
                // proper UTF-8 strings (e.g. Persian brand names).
                foreach ($params as $k => $v) {
                    $params[$k] = rawurldecode($v);
                }
                $this->dispatch($action, $params);
                return;
            }
        }

        http_response_code(404);
        echo "Page Not Found";
    }

    private function dispatch($action, $params = [])
    {
        if(is_array($action)){
            $controller = new $action[0]();
            $method = $action[1];
            if (method_exists($controller, $method)) {
                try {
                    $ref = new \ReflectionMethod($controller, $method);
                    $callArgs = [];

                    // Map route parameters by name to the method signature. This avoids
                    // positional padding that can mask parameter-contract bugs.
                    foreach ($ref->getParameters() as $p) {
                        $pname = $p->getName();
                        if (array_key_exists($pname, $params)) {
                            $callArgs[] = $params[$pname];
                            continue;
                        }

                        if ($p->isDefaultValueAvailable()) {
                            $callArgs[] = $p->getDefaultValue();
                            continue;
                        }

                        if ($p->isOptional()) {
                            $callArgs[] = null;
                            continue;
                        }

                        // Required parameter missing — raise a clear error so we can
                        // fix the route/controller contract instead of silently masking it.
                        throw new \RuntimeException("Missing route parameter '{$pname}' for {$action[0]}::{$method}");
                    }
                } catch (\ReflectionException $e) {
                    // If reflection fails, fall back to positional mapping
                    $callArgs = array_values($params);
                }

                call_user_func_array([$controller, $method], $callArgs);
                return;
            }
        } else {
            if ($params) {
                call_user_func_array($action, array_values($params));
            } else {
                call_user_func($action);
            }
        }
    }
}

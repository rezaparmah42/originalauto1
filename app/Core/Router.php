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

        error_log('[router] run start; REQUEST_URI=' . ($_SERVER['REQUEST_URI'] ?? '') );

        $base = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
        // Normalize base: dirname() can return '.' on some setups. Treat '.' or
        // '/' as an empty base (i.e. no prefix to strip). Use a prefix-only
        // removal to avoid accidentally removing the base when it appears
        // elsewhere in the URI (str_replace removed all occurrences).
        if ($base === '.' || $base === '/') {
            $base = '';
        }

        if ($base !== '' && strpos($uri, $base) === 0) {
            $uri = substr($uri, strlen($base));
        }

        // Ensure a leading slash and normalize trailing slash to single '/'
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
        if (is_array($action)) {
            $controllerClass = $action[0];
            $method = $action[1];
            try {
                error_log('[router] dispatch controller=' . $controllerClass . ' method=' . $method);
                $controller = new $controllerClass();

                if (method_exists($controller, $method)) {
                    try {
                        $ref = new \ReflectionMethod($controller, $method);
                        $callArgs = [];

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

                            throw new \RuntimeException("Missing route parameter '{$pname}' for {$controllerClass}::{$method}");
                        }
                    } catch (\ReflectionException $e) {
                        $callArgs = array_values($params);
                    }

                    try {
                        call_user_func_array([$controller, $method], $callArgs);
                    } catch (\Throwable $e) {
                        error_log('[router] unhandled exception in controller: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                        http_response_code(500);
                        echo "500 Server Error";
                    }
                    return;
                }
            } catch (\Throwable $e) {
                // Catch instantiation errors (e.g. missing class, fatal in constructor)
                error_log('[router] dispatch failure: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                http_response_code(500);
                echo "500 Server Error";
                return;
            }
        } else {
            try {
                if ($params) {
                    call_user_func_array($action, array_values($params));
                } else {
                    call_user_func($action);
                }
            } catch (\Throwable $e) {
                error_log('[router] action failure: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                http_response_code(500);
                echo "500 Server Error";
            }
        }
    }
}

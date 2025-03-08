<?php

namespace Minic\Core;

class Router
{
    protected array $routes = [];

    public function addRoute(string $method, string $path, callable|string|array $handler): void
    {
        $normalizedPath = $this->normalizePath($path);
        $this->routes[$method][$normalizedPath] = [
            'handler' => $handler,
            'pattern' => $this->convertPattern($normalizedPath),
        ];
    }

    public function dispatch($app, string $requestUri, string $requestMethod)
    {
        $requestUri = $this->normalizePath(parse_url($requestUri, PHP_URL_PATH));
        $requestMethod = strtoupper($requestMethod);
        foreach ($this->routes[$requestMethod] ?? [] as $path => $route) {
           
            if (preg_match($route['pattern'], $requestUri, $matches)) {
                
                array_shift($matches);

                if (empty($matches) && strpos($route['pattern'], '(') !== false) {
                    continue;
                }

                $this->handleRequest($app, $route['handler'], $matches);
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }

    protected function convertPattern(string $path): string
    {
        $patterns = [
            'number'    => '\d+',                
            'slug'      => '[a-z0-9-]+',         
            'string'    => '[^/]+',             
            'filename'  => '[a-zA-Z0-9_.-]+',    
            'address'   => '.+',                 
            'any'       => '[a-zA-Z0-9_.-]+',  
        ];

        $regex = preg_replace_callback('/\{(\w+)(?::(\w+))?\}/', function ($matches) use ($patterns) {
            $paramName = $matches[1];  

            $type = $matches[2] ?? 'any';  
            $pattern = $patterns[$type] ?? '[^/]+';  
            return '(?P<' . $paramName . '>' . $pattern . ')';
        }, $path);
        return '#^' . $regex . '$#';
    }


    protected function handleRequest($app, callable|string|array $handler, array $params)
    {

        $paramsAssoc = [];
        foreach ($params as $key => $value) {
            if (!is_numeric($key)) {
                $paramsAssoc[$key] = $value;
            }
        }

        if (is_callable($handler)) {
            echo call_user_func($handler, $app, $paramsAssoc);
        } 
        
        elseif (is_string($handler) && str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler);
            if (class_exists($class) && method_exists($class, $method)) {
                echo call_user_func([new $class, $method], $app, $paramsAssoc);
            } else {
                http_response_code(500);
                echo "Error: Controller method $class@$method not found!";
            }
        } 
        
        // Xử lý kiểu array ['Class', 'method']
        elseif (is_array($handler) && count($handler) === 2) {
            [$class, $method] = $handler;
            if (class_exists($class) && method_exists($class, $method)) {
                echo call_user_func([new $class, $method], $app, $paramsAssoc);
            } else {
                http_response_code(500);
                echo "Error: Controller method $class@$method not found!";
            }
        } 
        
        // Nếu không hợp lệ
        else {
            http_response_code(500);
            echo "Error: Invalid route handler!";
        }
    }


    protected function normalizePath(string $path): string
    {
        return '/' . trim($path, '/');
    }

    public function dump_routes()
    {
        echo "<pre>";
            print_r($this->routes);
        echo "</pre>";
    }
}

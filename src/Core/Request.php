<?php

namespace Minic\Core;

class Request
{
    protected array $get;
    protected array $post;
    protected array $files;
    protected array $server;
    protected array $headers;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->headers = $this->parseHeaders();
    }

    protected function parseHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headerName = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }

    public function getMethod(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }
    public function getPort(): int
    {
        return $_SERVER['SERVER_PORT'];
    }
    public function getProtocol(): string 
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    }
    
    public function getUri(): string
    {
        return $this->server['REQUEST_URI'] ?? '/';
    }

    /**
     * Lấy URL path (bỏ query string) và chuẩn hóa về dạng không có `/` cuối cùng.
     * @return string
     */

    public function path(): string
    {
        $path = trim(parse_url($this->getUri(), PHP_URL_PATH), "\/");
        $script_dir = trim(dirname($_SERVER['SCRIPT_NAME']),"\/");
        $script_filename = basename($_SERVER['SCRIPT_NAME']);

        if (strpos($path, $script_dir) === 0) {
            $path = trim(substr($path, strlen($script_dir)),"/");
        }
        
        if (strpos($path, $script_filename) === 0) {
            $path = substr($path, strlen($script_filename));
        }

        return $path ?: "/";
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $this->get[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post);
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function header(string $key, mixed $default = null): mixed
    {
        $key = strtolower($key);
        return $this->headers[$key] ?? $default;
    }

    public function isJson(): bool
    {
        return $this->header('content-type') === 'application/json';
    }

    public function json(): array
    {
        if ($this->isJson()) {
            return json_decode(file_get_contents('php://input'), true) ?? [];
        }
        return [];
    }
    function get_base_url(string $path = ''): string
    {
        $protocol = $this->getProtocol();

        $host = $_SERVER['HTTP_HOST'];

        // $port = $this->getPort();
        // $portPart = ($port != 80 && $port != 443) ? ':' . $port : '';

        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\\/');

        // return $protocol . '://' . $host . $portPart . $basePath . '/' . ltrim($path, '/');
        return $protocol . '://' . $host . $basePath . '/' . ltrim($path, '/');

    }

}

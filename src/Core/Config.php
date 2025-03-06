<?php
namespace Minic\Core;

class Config
{
    protected array $config = [];
    protected string $configPath;

    public function __construct(string $configPath)
    {
        $this->configPath = rtrim($configPath, DIRECTORY_SEPARATOR);
        
        if (!is_dir($this->configPath)) {
            throw new \Exception("Config directory not found: " . $this->configPath);
        }

        foreach (glob($this->configPath . '/*.php') as $file) {
            $key = basename($file, '.php');

            $configData = require $file;

            if (!is_array($configData)) {
                throw new \Exception("Invalid config file: $file must return an array.");
            }

            $this->config[$key] = $configData;
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $config = $this->config;

        foreach ($keys as $key) {
            if (!isset($config[$key])) {
                return $default;
            }
            $config = $config[$key];
        }

        return $config;
    }

    public function all(): array
    {
        return $this->config;
    }
}

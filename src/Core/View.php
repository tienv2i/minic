<?php

namespace Minic\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class View
{
    protected Environment $twig;
    protected static array $globals = [];
    protected static array $functions = [];

    public function __construct(string $templatePath, array $options = [])
    {
        $loader = new FilesystemLoader($templatePath);
        $this->twig = new Environment($loader, $options);

  
    }

    public function render(string $template, array $data = []): string
    {

        if (!$this->twig) {
            throw new \Exception("Twig environment is not initialized. Call View::init() first.");
        }
              // Apply global variables
        foreach (self::$globals as $key => $value) {
            $this->twig->addGlobal($key, $value);
        }

        // Apply custom functions
        foreach (self::$functions as $name => $callback) {
            $this->twig->addFunction(new TwigFunction($name, $callback));
        }
        return $this->twig->render($template, $data);
    }

    public static function addGlobal(string $key, mixed $value): void
    {
        self::$globals[$key] = $value;
    }

    public static function addFunction(string $name, callable|string $callback): void
    {
        self::$functions[$name] = $callback;
    }

}

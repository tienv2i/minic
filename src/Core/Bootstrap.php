<?php

/**
 * @method static base_url($path = "") {}
 * @method static static($path = "") {}
 */

namespace Minic\Core;
class Bootstrap {
    
    protected Request $request;

    private static ?Bootstrap $instance = null;
    protected Config $config;
    protected Router $router;
    protected View $view;
    protected array $default_view_data;
    protected $session_id;
    protected $session;
    protected array $options = [];


    public function __construct (array $options = []) {
    
        self::$instance = $this;

        $this->startSession();
        
        $default_base_path = realpath(__DIR__."/../..");

        $default_options = [
            "base_path" => $default_base_path,
            "config_path" => realpath($default_base_path."/config"),
            "routes_path" => realpath($default_base_path."/src/routes.php"),
            "views_path" => realpath($default_base_path."/src/App/Views"),
            "helpers_path" => realpath($default_base_path."/src/App/Helpers"),

        ];

        $options = array_merge($default_options, $options);

        $this->config = new Config($options["config_path"]);
        $this->request = new Request();
        $this->router = new Router();
        $this->view = new View($options["views_path"]);

        $this->loadHelpers($options["helpers_path"]);
        $this->loadRoutes($options["routes_path"]);

        $this->default_view_data = array_merge([
            "app_name" => $this->config->get("app.app_name", "Minic framework"),
            "base_url" => base_url(),
            "static_url" => static_url(),
        ], $this->config->get('app',[]));

        
    }

    public static function getInstance () {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getRequest(): Request {
        return $this->request;
    }

    public function getConfig($name, $default = null) {
        if ($name == 'all') return $this->config->all();
            else return $this->config->get($name, $default);
    }
    public function loadRouteFile(string $filePath) {
        $routes = require $filePath;
        foreach ($routes as $request_method => $paths) {
            foreach ($paths as $path => $handler) {
                if (is_string($handler) && str_contains($handler, '@')) {
                    [$class, $method] = explode('@', $handler);
                    $handler = [$class, $method];
                }
                $this->router->addRoute(strtoupper($request_method), $path, $handler);
            }
        }


    }
    public function loadRoutes(string $routesPath) {
        $routesPath = rtrim($routesPath, DIRECTORY_SEPARATOR);
        if (is_file($routesPath)) {
            $this->loadRouteFile($routesPath);
        }
        else {
            foreach (glob($routesPath . '/*.php') as $file) {
                $this->loadRouteFile($file);
            }

        }
    }
    


    public function loadHelpers(string $helpersPath): void
    {
        $helpersPath = rtrim($helpersPath, DIRECTORY_SEPARATOR);
        foreach (glob($helpersPath . '/*.php') as $file) {
            require_once $file;
        }
    }

    public function render($name, $data = []): void {
        echo $this->view->render($name, array_merge($this->default_view_data, $data));
    }

    protected function startSession ($lifetime = 1800) {
        session_set_cookie_params($lifetime, "/", "", true, true);
        session_start();
        $this -> session = $_SESSION;
        if (isset($_SESSION['CREATED'])) {
            if (time() - $_SESSION['CREATED'] > $lifetime) {
                $this->endSession();
                
                session_start();
                session_regenerate_id(true);
            }
        }
    
        $_SESSION['CREATED'] = time(); 
    }

    protected function endSession () {
        $this -> session = null;
        session_unset();
        session_destroy();
    }

    public function getSession($key=null, $default="") {
        return $key === null ? $this->session : ($this->session[$key] ??  $default);
    }
    public function setSession($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function simpleAuth() {
        $authorized_auth_code = $this->getConfig("app.auth_code");

        if ($this->getSession('auth_code') === $authorized_auth_code) {
            return true;
        }
        else {
            
            $this->render('auth/simple_auth.html', [
                'current_url' => getCurrentUrl()
            ]);
            die();
        }
    }
    public function cleanAuth() {
        $this->setSession('auth_code', null);
        $this->setSession('authorized', false);
    }
    public static function auth_clean_view (Bootstrap $app, $params) {
        $app->cleanAuth();
        header('Location: /');
        die();
    }
    static function auth_view (Bootstrap $app, $params) {
        $request = $app->getRequest();
        $auth_code = $request->input('auth_code');
        $authorized_auth_code = $app->getConfig("app.auth_code");

        if ($auth_code === $authorized_auth_code) {
            $app->setSession('authorized', true);
            $app->setSession('auth_code', $auth_code);
        }

        header('Location: '.$request->input('current_url', "/"));
        die();
    }

    public function run_app () {
        
        $this->router->dispatch(
            $this,
            $this->request->path(),
            $this->request->getMethod()
        );
    }
}
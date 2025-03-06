<?php
namespace Minic\Core;

class Bootstrap {
    
    public Request $request;
    private static ?Bootstrap $instance = null;
    protected Config $config;
    protected Router $router;
    protected array $options = [];
    protected View $view;
    protected array $default_view_data;

    public function __construct (array $options = []) {

        self::$instance = $this;

        $default_base_path = realpath(__DIR__."/../..");

        $default_options = [
            "base_path" => $default_base_path,
            "config_path" => realpath($default_base_path."/config"),
            "routes_path" => realpath($default_base_path."/routes"),
            "views_path" => realpath($default_base_path."/src/App/Views"),
            "helpers_path" => realpath($default_base_path."/src/App/Helpers"),

        ];

        $options = array_merge($default_options, $options);

        $this->config = new Config($options["config_path"]);
        $this->request = new Request();
        $this->router = new Router();
        $this->view = new View($options["views_path"]);

        $this->load_helpers($options["helpers_path"]);
        $this->load_routes($options["routes_path"]);

        $this->default_view_data = [
            "app_name" => $this->config->get("app.app_name", "Minic framework"),
            "base_url" => base_url(),
            "static_url" => static_url(),
        ];

        
    }

    public static function get_instance () {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get_config($name, $default = null) {
        if ($name == 'all') return $this->config->all();
            else return $this->config->get($name, $default);
    }

    public function load_routes(string $routesPath) {
        $routesPath = rtrim($routesPath, DIRECTORY_SEPARATOR);
    
        foreach (glob($routesPath . '/*.php') as $file) {
            $routes = require $file;
    
            foreach ($routes as $method => $paths) {
                foreach ($paths as $path => $handler) {
                    if (is_string($handler) && str_contains($handler, '@')) {
                        [$class, $method] = explode('@', $handler);
                        $handler = [$class, $method];
                    }
    
                    $this->router->addRoute(strtoupper($method), $path, $handler);
                }
            }
        }
    }
    


    public function load_helpers(string $helpersPath): void
    {
        $helpersPath = rtrim($helpersPath, DIRECTORY_SEPARATOR);
        foreach (glob($helpersPath . '/*.php') as $file) {
            require_once $file;
        }
    }

    public function render($name, $data = []): void {
        echo $this->view->render($name, array_merge($this->default_view_data, $data));
    }
    public function run_app () {
        $this->router->dispatch(
            $this,
            $this->request->path(),
            $this->request->getMethod()
        );
    }
}
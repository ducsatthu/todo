<?php

namespace Dst\Todo\Core;

use Dst\Todo\Core\Contracts\App\Applications;
use Dst\Todo\Core\Router\Route;

class app implements Applications
{
    /**
     * Define namespace
     * @var string $controllerNamespace
     */
    protected $controllerNamespace = 'Dst\\Todo\\Controllers\\';

    /**
     * @var Route|null
     */
    protected $route;

    /**
     * @var null
     */
    private static $instance = NULl;

    /**
     * app constructor.
     */
    protected function __construct()
    {
        $this->route = Route::getInstance();
    }

    /**
     * Singleton load
     *
     * @return app|null
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Run app with route and map to controller
     *
     * @return mixed
     */
    public function run()
    {
        $route = $this->route->mapRoute();

        $className = $this->controllerNamespace.$route[0];

        $action = $route[1];

        $controller = call_user_func([$className, 'getInstance']);

        $controller->$action();
    }
}
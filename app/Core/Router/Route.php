<?php

namespace Dst\Todo\Core\Router;

use Dst\Todo\Core\Contracts\Route\Registrar;

class Route implements Registrar
{
    protected $defaultController = 'ErrorsController@index';

    protected $getRoute = [];
    protected $postRoute = [];
    protected $putRoute = [];
    protected $deleteRoute = [];

    private static $instance = NULl;

    protected function __construct(){}

    /**
     * Singleton load
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register a new GET route with the router.
     *
     * @param  string $uri
     * @param  string $action
     */
    public function get($uri, $action)
    {
        $this->getRoute = array_merge([$uri => $action], $this->getRoute);
    }

    /**
     * Register a new POST route with the router.
     *
     * @param  string $uri
     * @param  string $action
     */
    public function post($uri, $action)
    {
        $this->postRoute = array_merge([$uri => $action], $this->postRoute);
    }

    /**
     * Register a new PUT route with the router.
     *
     * @param  string $uri
     * @param  string $action
     */
    public function put($uri, $action)
    {
        $this->putRoute = array_merge([$uri => $action], $this->putRoute);
    }

    /**
     * Register a new DELETE route with the router.
     *
     * @param  string $uri
     * @param  string $action
     */
    public function delete($uri, $action)
    {
        $this->deleteRoute = array_merge([$uri => $action], $this->deleteRoute);
    }

    /**
     * get route uri and return controller & action name
     * @return array
     *
     * TODO: method path only one 1 path need refactor in the future
     */
    public function getRouteName(): array
    {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = trim($uri, '/');
        $uri = explode('/', $uri);

        $method = $_SERVER['REQUEST_METHOD'];
        if ($uri[0] === '') {
            $uri[0] = '/';
        }
        foreach ($uri as $item){
            $uri[] = explode('?', $item);
        }
        $methodVariable = strtolower($method).'Route';
        $controller = isset(($this->$methodVariable)[$uri[0]])?($this->$methodVariable)[$uri[0]]:($this->defaultController);

        return explode('@', $controller);
    }


    /**
     * Map route and include
     * @return mixed
     */
    public function mapRoute()
    {
        $route = $this->getRouteName();
        return $route;
    }
}
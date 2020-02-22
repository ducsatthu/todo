<?php

namespace Dst\Todo\Core\Contracts\Route;

interface Registrar
{
    /**
     * Register a new GET route with the router.
     *
     * @param  string $uri
     * @param  string $action
     */
    public function get($uri, $action);

    /**
     * Register a new POST route with the router.
     *
     * @param  string $uri
     * @param  string $action
     */
    public function post($uri, $action);

    /**
     * Register a new PUT route with the router.
     *
     * @param  string $uri
     * @param  string $action
     */
    public function put($uri, $action);

    /**
     * Register a new DELETE route with the router.
     *
     * @param  string $uri
     * @param  string $action
     */
    public function delete($uri, $action);

    /**
     * get route uri and return controller & action name
     * @return array
     */
    public function getRouteName(): array;

    /**
     * Map route and include
     * @return mixed
     */
    public function mapRoute();
}

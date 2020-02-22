<?php

namespace Dst\Todo\Core\Contracts\App;

interface Applications
{
    /**
     * Run app with route and map to controller
     *
     * @return mixed
     */
    public function run();

    /**
     * Singleton class
     *
     * @return mixed
     */
    public static function getInstance();
}
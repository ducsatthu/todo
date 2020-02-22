<?php

namespace Dst\Todo\Controllers;

use Dst\Todo\Core\Controllers\BaseController;

class ErrorsController extends BaseController
{
    private static $instance = NULl;
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
     * Show page not found
     */
    public function index()
    {
        echo "Page not found";
    }
}
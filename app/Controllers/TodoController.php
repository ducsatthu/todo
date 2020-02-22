<?php

namespace Dst\Todo\Controllers;

use Dst\Todo\Core\Controllers\BaseController;

class TodoController extends BaseController
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

    public function index(){
        $this->render('index', ['test']);
    }
    public function edit(){
        echo "Hello edit";
    }
    public function add(){
        $this->render('add');
    }
    public function store(){
        var_dump($_POST);
    }
}
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

    /**
     * Show calendar
     */
    public function index(){
        $this->render('index', ['test']);
    }
    public function edit(){
        echo "Hello edit";
    }

    /**
     * Show form add new
     */
    public function add(){
        $this->render('add');
    }

    /**
     * Store
     */
    public function store(){
        /**
         * TODO: CSRF Token
         */
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'ilovedeveloper' ) {
            $json = json_decode(file_get_contents("php://input"));
        }else{
            //TODO: 404 return
            $this->return404();
        }
    }
}
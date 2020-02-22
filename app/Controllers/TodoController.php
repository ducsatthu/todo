<?php

namespace Dst\Todo\Controllers;

use Dst\Todo\Core\Controllers\BaseController;
use Dst\Todo\Models\Todo;

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
    public function index()
    {
        $this->render('index');
    }

    /**
     * Ajax get data
     */
    public function filter()
    {
        /**
         * TODO: CSRF Token
         */
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'ilovedeveloper') {
            $input = json_decode(file_get_contents("php://input"));
            if (is_object($input) && isset($input->start) && isset($input->end)) {
                $Todo = new Todo();
                $data = $Todo->find("`start` >= '$input->start' AND `end` <= '$input->end'");

                $this->returnJson($data);
            } else {
                $this->return404();
            }
        } else {
            //TODO: 404 return
            $this->return404();
        }
    }

    /**
     * @throws \Exception
     */
    public function edit()
    {
        $todo = new Todo();

        $todo->name = 'ABC';
        $todo->startDate = (new \DateTime())->format('Y-m-d');
        $todo->endDate = (new \DateTime())->format('Y-m-d');
        $todo->status = 0;
    }

    /**
     * Show form add new
     */
    public function add()
    {
        $this->render('add');
    }

    /**
     * Store
     */
    public function store()
    {
        /**
         * TODO: CSRF Token
         */
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'ilovedeveloper') {
            $input = json_decode(file_get_contents("php://input"));
            //TODO: validate rule
            if (is_object($input) && isset($input->name) && isset($input->status) && isset($input->start) && isset($input->end)) {
                $Todo = new Todo();
                $data = $Todo->find("`start` >= '$input->start' AND `end` <= '$input->end'");

                $this->returnJson($data);
            } else {
                $this->return404();
            }
        } else {
            //TODO: 404 return
            $this->return404();
        }
    }

    public function update()
    {
        /**
         * TODO: CSRF Token
         */
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'ilovedeveloper') {
            $input = json_decode(file_get_contents("php://input"));
            //TODO: validate rule
            if (is_object($input) && isset($input->name) && isset($input->status) && isset($input->start) && isset($input->end)) {
                $Todo = new Todo();
                $data = $Todo->find("`start` >= '$input->start' AND `end` <= '$input->end'");

                $this->returnJson($data);
            } else {
                $this->return404();
            }
        } else {
            //TODO: 404 return
            $this->return404();
        }
    }
}
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
     * Show form add new
     */
    public function add()
    {
        $this->render('add');
    }

    /**
     * Ajax get data
     */
    public function filter()
    {
        /**
         * TODO: CSRF Token
         */
        try {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'ilovedeveloper') {
                $input = json_decode(file_get_contents("php://input"));
                if (is_object($input) && isset($input->start) && isset($input->end)) {
                    $range = $this->compare($input->start, $input->end);
                    if (is_object($range)) {
                        $Todo = new Todo();
                        $data = $Todo->find("`start` >= '$input->start' AND `end` <= '$input->end'");

                        $this->returnJson($data);
                    }
                }
            }
            //TODO: 404 return
            $this->return404();
        } catch (\Exception $e) {
            $this->return404();
        }
    }

    /**
     * Store
     */
    public function store()
    {
        /**
         * TODO: CSRF Token
         */
        try {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'ilovedeveloper') {
                $input = json_decode(file_get_contents("php://input"));
                //TODO: validate rule
                if (is_object($input) && isset($input->name) && isset($input->status) && isset($input->start) && isset($input->end)) {
                    $range = $this->compare($input->start, $input->end);
                    if (is_object($range)) {
                        $Todo = new Todo();
                        $Todo->name = $input->name;
                        $Todo->start = $range->start;
                        $Todo->end = $range->end;
                        $Todo->status = $input->status;

                        if ($Todo->save()) {
                            $this->returnJson([
                                'success' => true
                            ]);
                        }
                    }
                }
            }
            $this->return404();
        } catch (\Exception $e) {
            //TODO: 404 return
            $this->return404();
        }
    }

    /**
     * Function ajax update todo
     * @throws \Exception
     */
    public function update()
    {
        /**
         * TODO: CSRF Token
         */
        try {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'ilovedeveloper') {
                $input = json_decode(file_get_contents("php://input"));
                //TODO: validate rule
                $range = $this->compare($input->start, $input->end);
                if (is_object($input) && isset($input->id) && isset($input->name) && isset($input->status) && isset($input->start) && isset($input->end) && is_object($range)) {
                    //TODO: Validate ID exist
                    $todo = (new Todo())->find("`id` = '$input->id'");
                    if (!empty($todo) && $todo) {
                        $todoUpdate = $todo[0];
                        $todoUpdate->name = $input->name;
                        $todoUpdate->start = $range->start;
                        $todoUpdate->end = $range->end;
                        $todoUpdate->status = $input->status;
                        if ($todoUpdate->save()) {
                            $this->returnJson([
                                'success' => true
                            ]);
                        }
                    }
                }
            }
            $this->return404();
        } catch (\Exception $e) {
            $this->return404();
        }
    }

    /**
     * Function ajax delete record
     * @throws \Exception
     */
    public function delete()
    {
        /**
         * TODO: CSRF Token
         */
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'ilovedeveloper') {
            $input = json_decode(file_get_contents("php://input"));
            //TODO: validate rule
            if (is_object($input) && isset($input->id)) {
                //TODO: Validate ID exist
                //Check exits ID
                $todo = (new Todo())->find("`id` = '$input->id'");
                if (!empty($todo) && $todo) {
                    $todoDelete = $todo[0];
                    if ($todoDelete->destroy()) {
                        $this->returnJson([
                            'success' => true
                        ]);
                    }
                }
            }
        }
        $this->return404();
    }

    /**
     * Validate
     * @param $date
     * @return bool
     */
    protected function isDate($date)
    {
        return \DateTime::createFromFormat('Y-m-d', $date) !== FALSE;
    }

    /**
     * Compare date time
     * @param $start
     * @param $end
     * @return null|object
     */
    protected function compare($start, $end)
    {
        if ($this->isDate($start) && $this->isDate($end)) {
            if (strtotime($start) > strtotime($end)) {
                return (object)[
                    'start' => $end,
                    'end' => $start
                ];
            }
            return (object)[
                'start' => $start,
                'end' => $end
            ];
        }
        return null;
    }
}
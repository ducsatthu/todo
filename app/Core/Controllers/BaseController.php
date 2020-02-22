<?php

namespace Dst\Todo\Core\Controllers;

class BaseController
{
    /**
     * Return 404 page
     */
    public function return404(){
        header("HTTP/1.0 404 Not Found");
        echo "Page not found";
        exit();
    }
    /**
     * @param string $view
     * @param $data
     */
    public function render($view = 'index', $data = [])
    {
        $view_file = __DIR__.'/../../Views/Pages/' . $view . '.php';

        if (is_file($view_file)) {
            extract($data);
            ob_start();
            require_once $view_file;
            $content = ob_get_clean();
            ob_clean();
            require_once __DIR__.'/../../Views/Layouts/default.php';
            exit();
        }

        $this->return404();
    }

    /**
     * return json data
     * @param $data
     */
    public function returnJson($data){
        header('Content-Type: application/json');
        echo json_encode($data);
        die();
    }
}
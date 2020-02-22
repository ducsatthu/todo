<?php

namespace Dst\Todo\Core\Controllers;

class BaseController
{
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
        }
    }
}
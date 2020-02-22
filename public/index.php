<?php
/**
 * Todo List
 */

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
*/
require __DIR__.'/../vendor/autoload.php';

require_once __DIR__ .'/../route/web.php';

$app = \Dst\Todo\Core\app::getInstance();

$app->run();



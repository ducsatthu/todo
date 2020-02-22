<?php
use Dst\Todo\Core\Router\Route;

$Route = Route::getInstance();

$Route->get('/', 'TodoController@index');
$Route->get('edit', 'TodoController@edit');
$Route->get('add', 'TodoController@add');
$Route->post('add', 'TodoController@store');
<?php

use Dst\Todo\Core\Router\Route;

$Route = Route::getInstance();

$Route->get('/', 'TodoController@index');
$Route->get('edit', 'TodoController@edit');
$Route->get('add', 'TodoController@add');
$Route->post('add', 'TodoController@store');
$Route->post('filter', 'TodoController@filter');
$Route->put('update', 'TodoController@update');
$Route->delete('delete', 'TodoController@delete');
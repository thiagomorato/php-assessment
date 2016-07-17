<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/tasks', 'TasksController@index');
$app->post('/tasks', 'TasksController@create');
$app->get('/tasks/{id}', 'TasksController@show');
$app->patch('/tasks/{id}', 'TasksController@update');
$app->delete('/tasks/{id}', 'TasksController@delete');


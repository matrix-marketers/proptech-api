<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/key', function() {
    return \Illuminate\Support\Str::random(32);
});

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/login','LoginController@login');
$router->post('/register','LoginController@register');

$router->get('/classes','ClassesController@getClasses');
$router->post('/classes','ClassesController@createClass');

$router->get('/students','StudentsController@getStudents');
$router->post('/students','StudentsController@createStudent');
$router->delete('/students/{id}','StudentsController@deleteStudent');

$router->post('/enrollstudent','StudentsController@enrollStudent');
$router->post('/unenrollstudent','StudentsController@unenrollStudent');

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->get('/article/getitem', 'Article@getItem');
$router->get('/article/list', 'Article@getList');
$router->put('/article/insert', 'Article@insert');
$router->delete('/article/delete', 'Article@delete');
$router->get('/tag/list', 'Tag@getList');
$router->put('/tag/insert', 'Tag@insert');
$router->get('/captcha/img', 'Captcha@img');
$router->get('/captcha/verify', 'Captcha@verify');
$router->get('/user/login', 'User@login');
$router->get('/todo/getitem', 'ToDo@getItem');
$router->get('/todo/getlist', 'ToDo@getList');
$router->post('/todo/insert', 'ToDo@insert');
$router->put('/todo/update', 'ToDo@update');
$router->put('/todo/updateorderby', 'ToDo@updateOrderBy');
//$router->delete('/tag/delete', 'Tag@delete');
//$router->group(['middleware' => 'auth'], function () use ($router) {
//    $router->get('/', function ()    {
//        // Uses Auth Middleware
//    });
//
//    $router->get('user/profile', function () {
//        // Uses Auth Middleware
//    });
//    $router->get('/article', 'Article@getItem');
//    $router->get('/article/list/{tags}', 'Article@getList');
//});
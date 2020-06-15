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
$router->group(['prefix' => 'api'], function () use ($router) {
    $router->group(['prefix' => 'v2'], function () use ($router) {
        $router->post('register', 'V2\AuthController@register');
        $router->post('login-client', 'V2\AuthController@loginClient');
        $router->post('login-provider', 'V2\AuthController@loginProvider');
    });
});

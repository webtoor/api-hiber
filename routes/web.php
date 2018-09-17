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

/* $router->get('/', function () use ($router) {
    return $router->app->version();
}); */
/* $router->group(['prefix' => '/'], function () use ($router) {
    $router->post('login', ['uses' => 'UserController@login']);
    $router->get('logins', ['uses' => 'UserController@logins']);
  }); */

$router->post('login', ['uses' => 'LoginController@login']);
$router->post('register', ['uses' => 'AuthController@register']);

$router->group(['prefix' => 'api', 'middleware' => 'auth:api'], function () use ($router) {
    $router->get('show', ['uses' => 'UserController@show']);
  });
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
 


$router->post('login_user', ['uses' => 'AuthController@login_user']);
$router->post('login_provider', ['uses' => 'AuthController@login_provider']);

$router->post('register', ['uses' => 'AuthController@register']);

$router->group(['prefix' => 'api', 'middleware' => 'auth:api'], function () use ($router) {
    $router->get('logout', ['uses' => 'AuthController@logout']);

    /* USER  */
    $router->group(['prefix' => 'user'], function () use ($router) {
        $router->post('order', ['uses' => 'OrderController@create']);
        $router->get('order_show/{user_id}', ['uses' => 'ProjectController@show']);
        $router->put('order_status/{order_id}', ['uses' => 'ProjectController@updateStatus']);
        $router->get('polygon/{order_id}', ['uses' => 'ProjectController@showPolygon']);
        $router->get('order_history/{user_id}', ['uses' => 'ProjectController@history']);
        $router->get('order_proposal/{order_id}', ['uses' => 'ProjectController@proposal']);
        $router->get('get_rating/{order_id}', ['uses' => 'ProjectController@getrating']);
        $router->post('order_feedback/{order_id}', ['uses' => 'ProjectController@feedback']);
      });
  });


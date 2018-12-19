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
$router->post('login_admin', ['uses' => 'AuthController@login_admin']);

//$router->get('timezone', ['uses' => 'OrderController@timezone']);

$router->post('register', ['uses' => 'AuthController@register']);

$router->group(['prefix' => 'api', 'middleware' => 'auth:api'], function () use ($router) {
    $router->get('logout', ['uses' => 'AuthController@logout']);

    /* USER  */
    $router->group(['prefix' => 'user'], function () use ($router) {
        $router->post('order', ['uses' => 'OrderController@create']);
        $router->get('order_baru/{user_id}', ['uses' => 'ProjectController@baru_show']);
        $router->get('order_berjalan/{user_id}', ['uses' => 'ProjectController@berjalan_show']);
        $router->put('order_status/{order_id}', ['uses' => 'ProjectController@updateStatus']);
        $router->get('history_project/{provider_id}', ['uses' => 'ProjectController@historyProject']);
        $router->get('polygon/{order_id}', ['uses' => 'ProjectController@showPolygon']);
        $router->get('order_proposal/{order_id}/{filter}', ['uses' => 'ProjectController@proposal']);
        $router->get('get_rating/{order_id}', ['uses' => 'ProjectController@getrating']);
        $router->post('order_feedback/{order_id}', ['uses' => 'ProjectController@feedback']);
        $router->get('order_history/{user_id}', ['uses' => 'ProjectController@history']);
        $router->get('profil_provider/{user_id}', ['uses' => 'ProjectController@profilProvider']);
    });
     
    /* SERVICE PROVIDER   */  
    $router->group(['prefix' => 'provider'], function () use($router){
        $router->get('tawaran_show/{provider_id}', ['uses' => 'ProviderProjectController@tawaranShow']);
        $router->get('detail_show/{order_id}', ['uses' => 'ProviderProjectController@detailShow']);
        $router->post('bidding', ['uses' => 'ProviderProjectController@bidding']);
        $router->get('berjalan_ikuti_show/{provider_id}', ['uses' => 'ProviderProjectController@berjalanIkutiShow']);
        $router->get('berjalan_kerja_show/{provider_id}', ['uses' => 'ProviderProjectController@berjalanKerjaShow']);
        $router->get('get_rating/{provider_id}', ['uses' => 'ProviderProjectController@getRatingShow']);
        $router->get('order_feedback/{provider_id}', ['uses' => 'ProviderProjectController@orderFeedbackShow']);
    });
     /* ADMIN   */  
     $router->group(['prefix' => 'admin'], function () use($router){
        $router->get('user_show', ['uses' => 'AdminController@userShow']);
        $router->get('order_show', ['uses' => 'AdminController@orderShow']);
        $router->get('order_detail_show/{id_order}', ['uses' => 'AdminController@orderDetailShow']);
        $router->get('provider_show', ['uses' => 'AdminController@providerShow']);
    });
  });


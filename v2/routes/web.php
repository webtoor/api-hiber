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

/* AUTH BEFORE UPGRADE   */
$router->post('login_user', ['uses' => 'V1\AuthController@login_user']);
$router->post('login_provider', ['uses' => 'V1\AuthController@login_provider']);
$router->post('login_admin', ['uses' => 'V1\AuthController@login_admin']);
$router->get('check', ['uses' => 'V1\AuthController@check']);
/* AUTH BEFORE UPGRADE   */

$router->get('export_latlng/{order_id}', ['uses' => 'V2\ProviderController@exportLatLong']);

$router->group(['prefix' => 'api'], function () use ($router) {


    $router->group(['prefix' => 'v2'], function () use ($router) {
        $router->post('register', 'V2\AuthController@register');
        $router->post('login-client', 'V2\AuthController@loginClient');
        $router->post('login-provider', 'V2\AuthController@loginProvider');


          /* SERVICE PROVIDER V2   */
        $router->group(['prefix' => 'provider', 'middleware' => ['auth:api', 'droner_hiber']], function () use($router){
            $router->get('offer/{projecttype}', ['uses' => 'V2\ProviderController@getOffer']);
            $router->get('offer-detail/{order_id}', ['uses' => 'V2\ProviderController@getOfferDetail']);
            $router->post('bid-offer', ['uses' => 'V2\ProviderController@postBidOffer']);
            $router->get('project-run-follow', ['uses' => 'V2\ProviderController@getProjectRunFollow']);

            $router->post('bid-cancel', ['uses' => 'V2\ProviderController@postBidCancel']);
            $router->get('rating', ['uses' => 'V2\ProviderController@getRating']);
            $router->get('project-run-work', ['uses' => 'V2\ProviderController@getProjectRunWork']);
            $router->get('order-feedback', ['uses' => 'V2\ProviderController@getOrderFeedback']);
            $router->post('offer-detail-email', ['uses' => 'V2\ProviderController@postOfferDetailSendEmail']);
        });

        $router->group(['prefix' => 'client', 'middleware' => ['auth:api', 'client_hiber']], function () use($router){
            $router->get('order-new', ['uses' => 'V2\ClientController@orderNew']);
            $router->get('order-run', ['uses' => 'V2\ClientController@orderRun']);
            $router->get('history-provider/{provider_id}', ['uses' => 'V2\ClientController@historyProvider']);
            $router->get('polygon/{order_id}', ['uses' => 'V2\ClientController@getPolygon']);
            $router->get('order-proposal/{order_id}/{filter}', ['uses' => 'V2\ClientController@getOrderProposal']);
            $router->get('order-rating/{order_id}', ['uses' => 'V2\ClientController@getOrderRating']);

        });

    });

    /* V1 ENDPOINT */
     /* USER BEFORE UPGRADE  */
    $router->get('logout', ['uses' => 'AuthController@logout']);
    $router->group(['prefix' => 'user', 'middleware' => ['client_hiber']], function () use ($router) {
        $router->post('order', ['uses' => 'OrderController@create']);
        $router->get('order_baru/{user_id}', ['uses' => 'V1\ProjectController@baru_show']);
        $router->get('order_berjalan/{user_id}', ['uses' => 'V1\ProjectController@berjalan_show']);
        $router->put('order_status/{order_id}', ['uses' => 'V1\ProjectController@updateStatus']);
        $router->get('history_provider/{provider_id}', ['uses' => 'V1\ProjectController@historyProvider']);
        $router->get('polygon/{order_id}', ['uses' => 'V1\ProjectController@showPolygon']);
        $router->get('order_proposal/{order_id}/{filter}', ['uses' => 'V1\ProjectController@proposal']);
        $router->get('get_rating/{order_id}', ['uses' => 'V1\ProjectController@getrating']);
        $router->post('order_feedback/{order_id}', ['uses' => 'V1\ProjectController@feedback']);
        $router->get('order_history/{user_id}', ['uses' => 'V1\ProjectController@history']);
        $router->get('profil_provider/{user_id}', ['uses' => 'V1\ProjectController@profilProvider']);
    });
         /* USER BEFORE UPGRADE  */


     /* SERVICE PROVIDER BEFORE UPGRADE  */
    $router->group(['prefix' => 'provider/v4', 'middleware' => ['auth:api', 'droner_hiber']], function () use($router){
        $router->get('tawaran_show/{provider_id}/{projecttype}', ['uses' => 'V1\ProviderProjectController@tawaranShow']);
        $router->get('detail_show/{order_id}', ['uses' => 'V1\ProviderProjectController@detailShow']);
        $router->post('bidding', ['uses' => 'V1\ProviderProjectController@bidding']);
        $router->get('berjalan_ikuti_show/{provider_id}', ['uses' => 'V1\ProviderProjectController@berjalanIkutiShow']);
        $router->post('cancel_bid', ['uses' => 'V1\ProviderProjectController@cancelBid']);
        //$router->post('edit_penawaran', ['uses' => 'V1\ProviderProjectController@editPenawaran']);

        $router->get('get_rating/{provider_id}', ['uses' => 'V1\ProviderProjectController@getRatingShow']);

        $router->get('berjalan_kerja_show/{provider_id}', ['uses' => 'V1\ProviderProjectController@berjalanKerjaShow']);
        $router->get('order_feedback/{provider_id}', ['uses' => 'V1\ProviderProjectController@orderFeedbackShow']);
        $router->post('send_email', ['uses' => 'V1\ProviderProjectController@sendEmail']);
    });
     /* SERVICE PROVIDER BEFORE UPGRADE  */


     /* ADMIN BEFORE UPGRADE  */
    $router->group(['prefix' => 'admin'], function () use($router){
        $router->get('user_show', ['uses' => 'AdminController@userShow']);
        $router->get('order_show', ['uses' => 'AdminController@orderShow']);
        $router->get('order_detail_show/{id_order}', ['uses' => 'AdminController@orderDetailShow']);
        $router->get('provider_show', ['uses' => 'AdminController@providerShow']);
    });
     /* ADMIN BEFORE UPGRADE  */
});

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
$router->post('login_user', ['uses' => 'V1\AuthController@login_user']);
$router->post('login_provider', ['uses' => 'V1\AuthController@login_provider']);
$router->post('login_admin', ['uses' => 'V1\AuthController@login_admin']);
$router->get('check', ['uses' => 'V1\AuthController@check']);
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
    });



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
});

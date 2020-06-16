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
});

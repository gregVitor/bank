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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api/v1'], function () use ($router) {

    $router->post('register', 'Auth\AuthController@registerUser');

    $router->post('login', 'Auth\AuthController@login');

    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->group(['prefix' => 'account'], function () use ($router) {
            $router->post('create', 'Account\AccountController@createAccount');
            $router->post('deposit', 'Account\AccountController@createAccountDeposit');
            $router->get('balance', 'Account\AccountController@getBalance');
            $router->get('extract', 'Account\AccountController@getExtract');
            $router->post('draft', 'Account\AccountController@draft');

        });

    });

});

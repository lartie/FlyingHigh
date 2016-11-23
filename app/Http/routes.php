<?php
/**
 * Copyright (c) FlyingHigh - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Artemy B. <artemy.be@gmail.com>, 24.11.2016
 */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$this->get('/', ['as' => 'root', 'uses' => 'WelcomeController@index']);

// Authentication Routes...
$this->group(['as' => 'auth.'], function() {
    $this->get('login', ['as' => 'login.get', 'uses' => 'Auth\AuthController@showLoginForm']);
    $this->post('login', ['as' => 'login.post', 'uses' => 'Auth\AuthController@login']);
    $this->get('logout', ['as' => 'logout.get', 'uses' => 'Auth\AuthController@logout']);

    // Registration Routes...
//    $this->get('register', 'Auth\AuthController@showRegistrationForm');
//    $this->post('register', 'Auth\AuthController@register');
});

// Password Reset Routes...
//$this->group(['as' => 'password.', 'prefix' => 'password'], function() {
//    $this->get('reset/{token?}', ['as' => 'reset.get', 'uses' => 'Auth\PasswordController@showResetForm']);
//    $this->post('email', ['as' => 'email.post', 'uses' => 'Auth\PasswordController@sendResetLinkEmail']);
//    $this->post('reset', ['as' => 'reset.post', 'uses' => 'Auth\PasswordController@reset']);
//});

//$this->get('/home', 'HomeController@index');

/**
 * Telegram API Bot routes
 */
Route::group(['prefix' => 'telegram', 'middleware' => 'web', 'as' => 'telegram'], function() {

    /**
     * Определение роутов для бота https://telegram.org/FlyingHighBot
     */
    Route::group(['prefix' => 'flyinghighbot', 'as' => '.flyinghighbot'], function () {

        /**
         *  Google Auth
         */
        Route::get('google/auth/{id}/{token}', ['as' => '.google.auth', 'uses' => 'GoogleController@auth']);
        Route::get('google/callback', ['as' => '.google.callback', 'uses' => 'GoogleController@callback']);

        Route::get('google/refresh', ['as' => '.google.refresh', 'uses' => 'GoogleController@refreshToken']);

        /**
         * Google Pub/Sub Notification
         */
        Route::post('google/webhook/' . config('telegrambot.token'), ['as' => '.google.webhook', 'uses' => 'GoogleController@webhook']);

        /**
         * FlightStats alerts
         */
        Route::any('flightstats/webhook/' . config('telegrambot.token'), ['as' => '.flightstats.webhook', 'uses' => 'FlightStatsController@webhook']);
    });
});

Route::get('google57ecd85802996427.html', ['as' => '.google.confirm', 'uses' => 'GoogleController@confirm']);

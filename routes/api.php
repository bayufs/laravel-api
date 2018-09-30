<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['prefix' => 'v1'], function () {

        Route::get('meeting', 'MeetingController@index');
        Route::post('meeting', 'MeetingController@store');
        Route::get('meeting/{id}', 'MeetingController@show');

        // authentication API menggunakan laravel passport
            Route::group(['middleware' => 'auth:api'], function(){
                Route::post('meeting/registration',  'RegisterController@store');
                Route::get('user/logout', ['as' => 'logout', 'uses' => 'API\UserController@logout']);
                Route::delete('meeting/registration/{id}',['as' => 'unregister', 'uses' => 'RegisterController@destroy']);
            });



    Route::post('user/register', [
        'uses' => 'AuthController@store'
    ]);
    
    Route::post('user/signin', [
    'uses' => 'AuthController@signin'
    ]);
    Route::post('user/login', 'API\UserController@login');
    Route::post('user/register', 'API\UserController@register');

    
    
});



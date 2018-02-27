<?php

use Dingo\Api\Routing\Router;
// use Auth;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['prefix' => 'auth'], function(Router $api) {
        $api->post('signup', 'App\\Api\\V1\\Controllers\\SignUpController@signUp');
        $api->post('login', 'App\\Api\\V1\\Controllers\\LoginController@login');

        $api->post('recovery', 'App\\Api\\V1\\Controllers\\ForgotPasswordController@sendResetEmail');
        $api->post('reset', 'App\\Api\\V1\\Controllers\\ResetPasswordController@resetPassword');
    });

    $api->group(['middleware' => 'jwt.auth'], function(Router $api) {
        
        $api->get('protected', function() {
            return response()->json([
                'message' => 'Access to protected resources granted! You are seeing this text as you provided the token correctly.'
            ]);
        });

        $api->get('refresh', [
            'middleware' => 'jwt.refresh',
            function() {
                return response()->json([
                    'message' => 'By accessing this endpoint, you can refresh your access token at each request. Check out this response headers!'
                ]);
            }
        ]);

        $api->get('auth/me', 
            function() {
                return response()->json(
                    Auth::user()
                );
            }
        );



    });

    $api->get('hello', function() {
        return response()->json([
            'message' => 'This is a simple example of item returned by your APIs. Everyone can see it.'
        ]);
    });

    //route for daily outputs
    Route::prefix('daily_outputs')->group(function(){
        Route::get('/', 'mainController@index' );
        Route::get('/download', 'mainController@download' );
        Route::post('/', 'mainController@store' );
        Route::get('/{id}', 'mainController@show' );
        Route::delete('/{id}', 'mainController@delete' );
        Route::put('/{id}', 'mainController@update' );

    });

    //route for daily outputs
    Route::prefix('lost_times')->group(function(){
        Route::get('/', 'LostTimeController@index' );
        Route::post('/', 'LostTimeController@store' );
        // Route::get('/{id}', 'LostTimeController@show' );
        Route::delete('/{id}', 'LostTimeController@destroy' );
        Route::put('/{id}', 'LostTimeController@update' );
    });

    //routes for daily repairs
    Route::prefix('daily_repairs')->group(function(){
        Route::get('/', 'DailyRepairController@index' );
        
        Route::post('/', 'DailyRepairController@store' );
        Route::delete('/{id}', 'DailyRepairController@destroy' );
        Route::put('/{id}', 'DailyRepairController@update' );

        Route::get('/perline', 'DailyRepairController@getPerLine' );
        Route::get('/permonth', 'DailyRepairController@getPerMonth' );
        Route::get('/{id}', 'DailyRepairController@show' ); //it should be the last. since we need to get per month and per line


        
    });

    Route::prefix('qualities')->group(function(){
        Route::get('/', 'QualityController@index' );
        Route::get('/dic', 'QualityController@getDIC' );
        // Route::get('/raw', 'QualityController@index' );

    });

});

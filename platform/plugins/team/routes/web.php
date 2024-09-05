<?php

use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function () {
    Route::group(['namespace' => 'Botble\Team\Http\Controllers'], function () {
        Route::group(['prefix' => 'teams', 'as' => 'team.'], function () {
            Route::resource('', 'TeamController')->parameters(['' => 'team']);
        });
    });
});

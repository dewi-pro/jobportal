<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Location\Http\Controllers'], function () {
    AdminHelper::registerRoutes(function () {
        Route::group(['prefix' => 'countries', 'as' => 'country.'], function () {
            Route::resource('', 'CountryController')->parameters(['' => 'country']);

            Route::get('list', [
                'as' => 'list',
                'uses' => 'CountryController@getList',
                'permission' => 'country.index',
            ]);
        });

        Route::group(['prefix' => 'states', 'as' => 'state.'], function () {
            Route::resource('', 'StateController')->parameters(['' => 'state']);

            Route::get('list', [
                'as' => 'list',
                'uses' => 'StateController@getList',
                'permission' => 'state.index',
            ]);
        });

        Route::group(['prefix' => 'cities', 'as' => 'city.'], function () {
            Route::resource('', 'CityController')->parameters(['' => 'city']);

            Route::get('list', [
                'as' => 'list',
                'uses' => 'CityController@getList',
                'permission' => 'city.index',
            ]);
        });

        Route::prefix('location')->name('location.')->group(function () {
            Route::post('upload/process', [
                'as' => 'upload.process',
                'uses' => 'ChunkUploadController@__invoke',
            ]);

            Route::post('upload/validate', [
                'as' => 'upload.validate',
                'uses' => 'ChunkValidateController@__invoke',
            ]);

            Route::post('import', [
                'as' => 'import',
                'uses' => 'ChunkImportController@__invoke',
            ]);
        });

        Route::group(['prefix' => 'locations/bulk-import', 'as' => 'location.bulk-import.'], function () {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'BulkImportController@index',
            ]);

            Route::post('/download-template', [
                'as' => 'download-template',
                'uses' => 'BulkImportController@downloadTemplate',
                'permission' => 'location.bulk-import.index',
            ]);

            Route::post('/import-location-data', [
                'as' => 'import-location-data',
                'uses' => 'BulkImportController@importLocationData',
                'permission' => 'location.bulk-import.index',
            ]);
        });

        Route::group(['prefix' => 'locations/export', 'as' => 'location.export.'], function () {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'ExportController@index',
            ]);

            Route::post('/', [
                'as' => 'process',
                'uses' => 'ExportController@export',
                'permission' => 'location.export.index',
            ]);
        });
    });

    Theme::registerRoutes(function () {
        Route::get('ajax/states-by-country', 'StateController@ajaxGetStates')
            ->name('ajax.states-by-country');
        Route::get('ajax/cities-by-state', 'CityController@ajaxGetCities')
            ->name('ajax.cities-by-state');
    });
});

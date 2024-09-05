<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Blog\Http\Controllers'], function () {
    AdminHelper::registerRoutes(function () {
        Route::group(['prefix' => 'blog'], function () {
            Route::group(['prefix' => 'posts', 'as' => 'posts.'], function () {
                Route::resource('', 'PostController')
                    ->parameters(['' => 'post']);

                Route::get('widgets/recent-posts', [
                    'as' => 'widget.recent-posts',
                    'uses' => 'PostController@getWidgetRecentPosts',
                    'permission' => 'posts.index',
                ]);
            });

            Route::group(['prefix' => 'categories', 'as' => 'categories.'], function () {
                Route::resource('', 'CategoryController')
                    ->parameters(['' => 'category']);

                Route::put('update-tree', [
                    'as' => 'update-tree',
                    'uses' => 'CategoryController@updateTree',
                    'permission' => 'categories.index',
                ]);
            });

            Route::group(['prefix' => 'tags', 'as' => 'tags.'], function () {
                Route::resource('', 'TagController')
                    ->parameters(['' => 'tag']);

                Route::get('all', [
                    'as' => 'all',
                    'uses' => 'TagController@getAllTags',
                    'permission' => 'tags.index',
                ]);
            });
        });

        Route::group(['prefix' => 'settings/blog', 'as' => 'blog.settings', 'permission' => 'blog.settings'], function () {
            Route::get('/', [
                'uses' => 'Settings\BlogSettingController@edit',
            ]);

            Route::put('/', [
                'as' => '.update',
                'uses' => 'Settings\BlogSettingController@update',
            ]);
        });
    });

    if (defined('THEME_MODULE_SCREEN_NAME')) {
        Theme::registerRoutes(function () {
            Route::get('search', [
                'as' => 'public.search',
                'uses' => 'PublicController@getSearch',
            ]);
        });
    }
});

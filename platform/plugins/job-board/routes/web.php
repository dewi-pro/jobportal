<?php

use Botble\Base\Facades\AdminHelper;
use Botble\JobBoard\Http\Controllers\AccountEducationController;
use Botble\JobBoard\Http\Controllers\AccountExperienceController;
use Botble\JobBoard\Http\Controllers\CouponController;
use Botble\JobBoard\Http\Controllers\CustomFieldController;
use Botble\JobBoard\Http\Controllers\ExportAccountController;
use Botble\JobBoard\Http\Controllers\ImportAccountController;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function () {
    Route::group(['namespace' => 'Botble\JobBoard\Http\Controllers', 'prefix' => 'job-board', 'middleware' => 'auth'], function () {
        Route::prefix('settings')->name('job-board.settings.')->group(function () {
            Route::get('general', [
                'as' => 'general',
                'uses' => 'Settings\GeneralSettingController@edit',
            ]);

            Route::put('general', [
                'as' => 'general.update',
                'uses' => 'Settings\GeneralSettingController@update',
                'permission' => 'job-board.settings',
            ]);

            Route::get('currencies', [
                'as' => 'currencies',
                'uses' => 'Settings\CurrencySettingController@edit',
                'permission' => 'job-board.settings',
            ]);

            Route::put('currencies', [
                'as' => 'currencies.update',
                'uses' => 'Settings\CurrencySettingController@update',
                'permission' => 'job-board.settings',
            ]);

            Route::get('invoices', [
                'as' => 'invoices',
                'uses' => 'Settings\InvoiceSettingController@edit',
                'permission' => 'job-board.settings',
            ]);

            Route::put('invoices', [
                'as' => 'invoices.update',
                'uses' => 'Settings\InvoiceSettingController@update',
                'permission' => 'job-board.settings',
            ]);

            Route::get('invoice-template', [
                'as' => 'invoice-template',
                'uses' => 'Settings\InvoiceTemplateSettingController@edit',
                'permission' => 'invoice-template.index',
            ]);

            Route::put('invoice-template', [
                'as' => 'invoice-template.update',
                'uses' => 'Settings\InvoiceTemplateSettingController@update',
                'permission' => 'invoice-template.index',
                'middleware' => 'preventDemo',
            ]);

            Route::post('invoice-template/reset', [
                'as' => 'invoice-template.reset',
                'uses' => 'Settings\InvoiceTemplateSettingController@reset',
                'permission' => 'invoice-template.index',
                'middleware' => 'preventDemo',
            ]);

            Route::get('invoice-template/preview', [
                'as' => 'invoice-template.preview',
                'uses' => 'Settings\InvoiceTemplateSettingController@preview',
                'permission' => 'invoice-template.index',
            ]);
        });

        Route::group(['prefix' => 'jobs', 'as' => 'jobs.'], function () {
            Route::resource('', 'JobController')->parameters(['' => 'job']);

            Route::get('{id}/analytics', [
                'as' => 'analytics',
                'uses' => 'JobController@analytics',
                'permission' => 'jobs.index',
            ])->wherePrimaryKey();
        });

        Route::group(['prefix' => 'recruitment', 'as' => 'recruitment.'], function () {
            Route::resource('', 'RecruitmentProgressController')
                ->parameters(['' => 'recruitment']);
        });

        Route::group(['prefix' => 'job-types', 'as' => 'job-types.'], function () {
            Route::resource('', 'JobTypeController')
                ->parameters(['' => 'job-type']);
        });

        Route::group(['prefix' => 'job-skills', 'as' => 'job-skills.'], function () {
            Route::resource('', 'JobSkillController')->parameters(['' => 'job-skill']);
        });

        Route::group(['prefix' => 'job-shifts', 'as' => 'job-shifts.'], function () {
            Route::resource('', 'JobShiftController')->parameters(['' => 'job-shift']);
        });

        Route::group(['prefix' => 'job-experiences', 'as' => 'job-experiences.'], function () {
            Route::resource('', 'JobExperienceController')->parameters(['' => 'job-experience']);
        });

        Route::group(['prefix' => 'language-levels', 'as' => 'language-levels.'], function () {
            Route::resource('', 'LanguageLevelController')->parameters(['' => 'language-level']);
        });

        Route::group(['prefix' => 'career-levels', 'as' => 'career-levels.'], function () {
            Route::resource('', 'CareerLevelController')
                ->parameters(['' => 'career-level']);
        });

        Route::group(['prefix' => 'functional-areas', 'as' => 'functional-areas.'], function () {
            Route::resource('', 'FunctionalAreaController')
                ->parameters(['' => 'functional-area']);
        });

        Route::group(['prefix' => 'job-categories', 'as' => 'job-categories.'], function () {
            Route::resource('', 'CategoryController')
                ->parameters(['' => 'job-category']);

            Route::put('update-tree', [
                'as' => 'update-tree',
                'uses' => 'CategoryController@updateTree',
                'permission' => 'job-categories.edit',
            ]);
        });

        Route::group(['prefix' => 'degree-types', 'as' => 'degree-types.'], function () {
            Route::resource('', 'DegreeTypeController')
                ->parameters(['' => 'degree-type']);
        });

        Route::group(['prefix' => 'degree-levels', 'as' => 'degree-levels.'], function () {
            Route::resource('', 'DegreeLevelController')
                ->parameters(['' => 'degree-level']);
        });

        Route::group(['prefix' => 'tags', 'as' => 'job-board.tag.'], function () {
            Route::resource('', 'TagController')
                ->parameters(['' => 'tag']);

            Route::get('all', [
                'as' => 'all',
                'uses' => 'TagController@getAllTags',
                'permission' => 'job-board.tag.index',
            ]);
        });

        Route::group(['prefix' => 'accounts', 'as' => 'accounts.'], function () {
            Route::resource('', 'AccountController')->parameters(['' => 'account']);

            Route::group(['prefix' => 'educations', 'as' => 'educations.'], function () {
                Route::resource('', AccountEducationController::class)->parameters(['' => 'education']);
                Route::get('{id}/{accountId}/edit-modal', [AccountEducationController::class, 'editModal'])->name(
                    'edit-modal'
                )->wherePrimaryKey(['id', 'accountId']);
            });

            Route::group(['prefix' => 'experiences', 'as' => 'experiences.'], function () {
                Route::resource('', AccountExperienceController::class)->parameters(['' => 'experience']);
                Route::get('{id}/{accountId}/edit-modal', [AccountExperienceController::class, 'editModal'])->name(
                    'edit-modal'
                )->wherePrimaryKey(['id', 'accountId']);
            });

            Route::get('list', [
                'as' => 'list',
                'uses' => 'AccountController@getList',
                'permission' => 'accounts.index',
            ]);

            Route::post('credits/{id}', [
                'as' => 'credits.add',
                'uses' => 'TransactionController@postCreate',
                'permission' => 'accounts.edit',
            ])->wherePrimaryKey();

            Route::get('all-employers', [
                'as' => 'all-employers',
                'uses' => 'AccountController@getAllEmployers',
                'permission' => 'accounts.index',
            ]);
        });

        Route::group(['prefix' => 'packages', 'as' => 'packages.'], function () {
            Route::resource('', 'PackageController')->parameters(['' => 'package']);
        });

        Route::group(['prefix' => 'companies', 'as' => 'companies.'], function () {
            Route::resource('', 'CompanyController')->parameters(['' => 'company']);

            Route::get('list', [
                'as' => 'list',
                'uses' => 'CompanyController@getList',
                'permission' => 'companies.index',
            ]);

            Route::get('all', [
                'as' => 'all',
                'uses' => 'CompanyController@getAllCompanies',
                'permission' => 'companies.index',
            ]);

            Route::get('{id}/analytics', [
                'as' => 'analytics',
                'uses' => 'CompanyController@analytics',
                'permission' => 'companies.index',
            ])->wherePrimaryKey();
        });

        Route::get('ajax/companies/{id}', [
            'as' => 'ajax.company.show',
            'uses' => 'CompanyController@ajaxGetCompany',
            'permission' => 'companies.index',
        ])->wherePrimaryKey();

        Route::post('ajax/companies', [
            'as' => 'ajax.company.create',
            'uses' => 'CompanyController@ajaxCreateCompany',
            'permission' => 'companies.create',
        ])->wherePrimaryKey();

        Route::group(['prefix' => 'job-applications', 'as' => 'job-applications.'], function () {
            Route::resource('', 'JobApplicationController')
                ->except(['create', 'store'])
                ->parameters(['' => 'job-application']);

            Route::get('download-cv/{id}', [
                'as' => 'download-cv',
                'uses' => 'JobApplicationController@downloadCv',
                'permission' => false,
            ])->wherePrimaryKey();
        });

        Route::group(['prefix' => 'invoices', 'as' => 'invoice.'], function () {
            Route::resource('', 'InvoiceController')
                ->parameters(['' => 'invoice'])
                ->except(['create', 'store', 'update']);

            Route::get('generate-invoice/{id}', [
                'as' => 'generate-invoice',
                'uses' => 'InvoiceController@getGenerateInvoice',
                'permission' => 'invoice.edit',
            ])->wherePrimaryKey();
        });

        Route::prefix('custom-fields')->name('job-board.custom-fields.')->group(function () {
            Route::resource('', CustomFieldController::class)->parameters(['' => 'custom-field']);

            Route::get('info', [
                'as' => 'get-info',
                'uses' => 'CustomFieldController@getInfo',
                'permission' => false,
            ]);
        });

        Route::group(['prefix' => 'import/jobs', 'as' => 'import-jobs.'], function () {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'ImportJobController@index',
            ]);

            Route::post('/', [
                'as' => 'store',
                'uses' => 'ImportJobController@store',
                'permission' => 'import-jobs.index',
            ]);

            Route::post('download-template', [
                'as' => 'download-template',
                'uses' => 'ImportJobController@downloadTemplate',
                'permission' => 'import-jobs.index',
            ]);
        });

        Route::group(['prefix' => 'export-jobs', 'as' => 'export-jobs.'], function () {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'ExportJobController@index',
                'permission' => 'export-jobs.index',
            ]);

            Route::post('/', [
                'as' => 'index.post',
                'uses' => 'ExportJobController@export',
                'permission' => 'export-jobs.index',
            ]);
        });

        Route::prefix('accounts')->name('accounts.')->group(function () {
            Route::get('import', [ImportAccountController::class, 'index'])->name('import');
            Route::post('import', [ImportAccountController::class, 'import']);
            Route::get('export', [ExportAccountController::class, 'index'])->name('export');
            Route::post('export', [ExportAccountController::class, 'export']);
            Route::post('download-template', [ImportAccountController::class, 'downloadTemplate'])->name('download-template');
        });

        Route::group(['prefix' => 'import/companies', 'as' => 'import-companies.'], function () {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'ImportCompanyController@index',
            ]);

            Route::post('/', [
                'as' => 'store',
                'uses' => 'ImportCompanyController@store',
                'permission' => 'import-companies.index',
            ]);

            Route::post('download-template', [
                'as' => 'download-template',
                'uses' => 'ImportCompanyController@downloadTemplate',
                'permission' => 'import-companies.index',
            ]);
        });

        Route::group(['prefix' => 'export-companies', 'as' => 'export-companies.'], function () {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'ExportCompanyController@index',
                'permission' => 'export-companies.index',
            ]);

            Route::post('/', [
                'as' => 'index.post',
                'uses' => 'ExportCompanyController@export',
                'permission' => 'export-companies.index',
            ]);
        });

        Route::group(['prefix' => 'coupons', 'as' => 'coupons.'], function () {
            Route::resource('', CouponController::class)
                ->parameters(['' => 'coupon']);

            Route::post('generate-coupon', [
                'as' => 'generate-coupon',
                'uses' => 'CouponController@generateCouponCode',
                'permission' => 'coupons.index',
            ]);
        });
    });
});

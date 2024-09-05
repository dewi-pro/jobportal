<?php

use Botble\JobBoard\Enums\AccountTypeEnum;
use Botble\JobBoard\Http\Controllers\CustomFieldController;
use Botble\JobBoard\Http\Controllers\Fronts\AccountEducationController;
use Botble\JobBoard\Http\Controllers\Fronts\AccountExperienceController;
use Botble\JobBoard\Http\Controllers\Fronts\CouponController;
use Botble\JobBoard\Http\Controllers\JobApplicationController;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\JobBoard\Http\Controllers'], function () {
    Theme::registerRoutes(function () {
        Route::group(['as' => 'public.account.', 'namespace' => 'Auth'], function () {
            Route::group(['middleware' => ['account.guest']], function () {
                Route::controller('LoginController')->group(function () {
                    Route::get('login', [
                        'as' => 'login',
                        'uses' => 'showLoginForm',
                    ]);
                    Route::post('login', [
                        'as' => 'login.post',
                        'uses' => 'login',
                    ]);
                });

                Route::controller('RegisterController')->group(function () {
                    Route::get('register', [
                        'as' => 'register',
                        'uses' => 'showRegistrationForm',
                    ]);

                    Route::post('register', [
                        'as' => 'register.post',
                        'uses' => 'register',
                    ]);
                });

                Route::group(['prefix' => 'password', 'as' => 'password.'], function () {
                    Route::controller('ForgotPasswordController')->group(function () {
                        Route::get('request', [
                            'as' => 'request',
                            'uses' => 'showLinkRequestForm',
                        ]);

                        Route::post('email', [
                            'as' => 'email',
                            'uses' => 'sendResetLinkEmail',
                        ]);
                    });

                    Route::controller('ResetPasswordController')->group(function () {
                        Route::get('reset/{token}', [
                            'as' => 'reset',
                            'uses' => 'showResetForm',
                        ]);

                        Route::post('reset', [
                            'as' => 'reset.update',
                            'uses' => 'reset',
                        ]);
                    });
                });
            });

            Route::group([
                'middleware' => [setting('verify_account_email', 0) ? 'account.guest' : 'account'],
            ], function () {
                Route::group(['prefix' => 'register/confirm'], function () {
                    Route::controller('RegisterController')->group(function () {
                        Route::get('resend', [
                            'as' => 'resend_confirmation',
                            'uses' => 'resendConfirmation',
                        ]);
                        Route::get('{email}', [
                            'as' => 'confirm',
                            'uses' => 'confirm',
                        ]);
                    });
                });
            });
        });

        Route::group([
            'middleware' => ['account'],
            'as' => 'public.account.',
            'namespace' => 'Auth',
        ], function () {
            Route::group(['prefix' => 'account'], function () {
                Route::post('logout', [
                    'as' => 'logout',
                    'uses' => 'LoginController@logout',
                ]);
            });
        });

        Route::group([
            'middleware' => ['account'],
            'as' => 'public.account.',
        ], function () {
            Route::prefix('account/custom-fields')->name('custom-fields.')->group(function () {
                Route::get('info', [CustomFieldController::class, 'getInfo'])->name('get-info');
            });
        });

        Route::group([
            'middleware' => ['account'],
            'as' => 'public.account.',
            'namespace' => 'Fronts',
        ], function () {
            Route::group(['prefix' => 'account'], function () {
                Route::controller('AccountController')->group(function () {
                    Route::get('overview', [
                        'as' => 'overview',
                        'uses' => 'getOverview',
                    ]);

                    Route::get('settings', [
                        'as' => 'settings',
                        'uses' => 'getSettings',
                    ]);

                    Route::post('settings', [
                        'as' => 'post.settings',
                        'uses' => 'postSettings',
                    ]);

                    Route::get('security', [
                        'as' => 'security',
                        'uses' => 'getSecurity',
                    ]);

                    Route::put('security', [
                        'as' => 'post.security',
                        'uses' => 'postSecurity',
                    ]);

                    Route::post('avatar', [
                        'as' => 'avatar',
                        'uses' => 'postAvatar',
                    ]);

                    Route::controller('AccountJobController')->group(function () {
                        Route::group([
                            'middleware' => ['account:' . AccountTypeEnum::JOB_SEEKER],
                            'as' => 'jobs.',
                        ], function () {
                            Route::get('applied-jobs', [
                                'as' => 'applied-jobs',
                                'uses' => 'appliedJobs',
                            ]);

                            Route::get('saved-jobs', [
                                'as' => 'saved',
                                'uses' => 'savedJobs',
                            ]);

                            Route::post('saved-jobs/action/{id?}', [
                                'as' => 'saved.action',
                                'uses' => 'savedJob',
                            ]);
                        });
                    });
                });
                Route::group(['prefix' => 'experiences', 'as' => 'experiences.'], function () {
                    Route::resource('', AccountExperienceController::class)->parameters(['' => 'experience']);
                });

                Route::group(['prefix' => 'educations', 'as' => 'educations.'], function () {
                    Route::resource('', AccountEducationController::class)->parameters(['' => 'education']);
                });
            });

            Route::group([
                'prefix' => 'account',
                'middleware' => ['account:' . AccountTypeEnum::EMPLOYER],
            ], function () {
                Route::get('dashboard', [
                    'as' => 'dashboard',
                    'uses' => 'DashboardController@index',
                ]);

                Route::group([
                    'prefix' => 'companies',
                    'as' => 'companies.',
                ], function () {
                    Route::resource('', 'CompanyController')->parameters(['' => 'companies']);
                });

                Route::group([
                    'prefix' => 'applicants',
                    'as' => 'applicants.',
                ], function () {
                    Route::resource('', 'ApplicantController')
                        ->parameters(['' => 'applicant'])
                        ->only(['index', 'edit', 'update']);
                });

                Route::get('applicants/download-cv/{id}', [JobApplicationController::class, 'downloadCv'])
                    ->name('applicants.download-cv')->wherePrimaryKey();

                Route::group([
                    'prefix' => 'jobs',
                    'as' => 'jobs.',
                ], function () {
                    Route::resource('', 'AccountJobController')->parameters(['' => 'job']);

                    Route::controller('AccountJobController')->group(function () {
                        Route::post('renew/{id}', [
                            'as' => 'renew',
                            'uses' => 'renew',
                        ])->wherePrimaryKey();

                        Route::get('{id}/analytics', [
                            'as' => 'analytics',
                            'uses' => 'analytics',
                        ])->wherePrimaryKey();

                        Route::get('tags/all', [
                            'as' => 'tags.all',
                            'uses' => 'getAllTags',
                        ]);
                    });
                });

                Route::group([
                    'prefix' => 'packages',
                    'middleware' => [
                        'account:' . AccountTypeEnum::EMPLOYER,
                        'enable-credits',
                    ],
                ], function () {
                    Route::controller('DashboardController')->group(function () {
                        Route::get('/', [
                            'as' => 'packages',
                            'uses' => 'getPackages',
                        ]);

                        Route::get('{id}/subscribe', [
                            'as' => 'package.subscribe',
                            'uses' => 'getSubscribePackage',
                        ])->wherePrimaryKey();

                        Route::get('{id}/subscribe/callback', [
                            'as' => 'package.subscribe.callback',
                            'uses' => 'getPackageSubscribeCallback',
                        ])->wherePrimaryKey();

                        Route::put('/', [
                            'as' => 'package.subscribe.put',
                            'uses' => 'subscribePackage',
                        ]);
                    });
                });

                Route::group([
                    'prefix' => 'invoices',
                    'as' => 'invoices.',
                ], function () {
                    Route::resource('', 'InvoiceController')
                        ->only('index')
                        ->parameters('invoices');
                    Route::get('{invoice}', 'InvoiceController@show')->name('show')->wherePrimaryKey();
                    ;
                    Route::get('{invoice}/generate-invoice', 'InvoiceController@getGenerateInvoice')
                        ->name('generate_invoice')
                        ->wherePrimaryKey();
                });
            });

            Route::group(['prefix' => 'ajax/accounts'], function () {
                Route::controller('AccountController')->group(function () {
                    Route::get('activity-logs', [
                        'as' => 'activity-logs',
                        'uses' => 'getActivityLogs',
                    ]);

                    Route::post('upload', [
                        'as' => 'upload',
                        'uses' => 'postUpload',
                    ]);

                    Route::post('upload-resume', [
                        'as' => 'upload-resume',
                        'uses' => 'postUploadResume',
                    ]);

                    Route::post('upload-from-editor', [
                        'as' => 'upload-from-editor',
                        'uses' => 'postUploadFromEditor',
                    ]);
                });

                Route::group([
                    'middleware' => [
                        'account:' . AccountTypeEnum::EMPLOYER,
                        'enable-credits',
                    ],
                ], function () {
                    Route::controller('DashboardController')->group(function () {
                        Route::get('transactions', [
                            'as' => 'ajax.transactions',
                            'uses' => 'ajaxGetTransactions',
                        ]);

                        Route::get('packages', [
                            'as' => 'ajax.packages',
                            'uses' => 'ajaxGetPackages',
                        ]);
                    });

                    Route::prefix('coupon')->name('coupon.')->group(function () {
                        Route::post('apply', [CouponController::class, 'apply'])->name('apply');
                        Route::post('remove', [CouponController::class, 'remove'])->name('remove');
                        Route::get('refresh/{id}', [CouponController::class, 'refresh'])->name('refresh');
                    });
                });
            });
        });
    });
});

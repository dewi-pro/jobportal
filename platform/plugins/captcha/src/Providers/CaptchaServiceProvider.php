<?php

namespace Botble\Captcha\Providers;

use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Captcha\Captcha;
use Botble\Captcha\CaptchaV3;
use Botble\Captcha\Facades\Captcha as CaptchaFacade;
use Botble\Captcha\MathCaptcha;
use Botble\Setting\PanelSections\SettingOthersPanelSection;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class CaptchaServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    protected bool $defer = false;

    public function register(): void
    {
        $this->app->singleton('captcha', function () {
            if (setting('captcha_type') === 'v3') {
                return new CaptchaV3(setting('captcha_site_key'), setting('captcha_secret'));
            }

            return new Captcha(setting('captcha_site_key'), setting('captcha_secret'));
        });

        $this->app->singleton('math-captcha', function ($app) {
            return new MathCaptcha($app['session']);
        });

        AliasLoader::getInstance()->alias('Captcha', CaptchaFacade::class);
    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/captcha')
            ->loadAndPublishConfigurations(['general', 'permissions'])
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations();

        $this->bootValidator();

        PanelSectionManager::default()->beforeRendering(function () {
            PanelSectionManager::registerItem(
                SettingOthersPanelSection::class,
                fn () => PanelSectionItem::make('captcha')
                    ->setTitle(trans('plugins/captcha::captcha.settings.title'))
                    ->withIcon('ti ti-refresh')
                    ->withPriority(150)
                    ->withDescription(trans('plugins/captcha::captcha.settings.panel_description'))
                    ->withRoute('captcha.settings')
            );
        });
    }

    public function bootValidator(): void
    {
        $app = $this->app;

        /**
         * @var Validator $validator
         */
        $validator = $app['validator'];
        $validator->extend('captcha', function ($attribute, $value, $parameters) use ($app) {
            if (! $app['captcha']->reCaptchaEnabled()) {
                return true;
            }

            if (! is_string($value)) {
                return false;
            }

            if (setting('captcha_type') === 'v3') {
                if (empty($parameters)) {
                    $parameters = ['form', (float) setting('recaptcha_score', 0.6)];
                }
            } else {
                $parameters = $this->mapParameterToOptions($parameters);
            }

            return $app['captcha']->verify($value, $this->app['request']->getClientIp(), $parameters);
        }, __('Captcha Verification Failed!'));

        $validator->extend('math_captcha', function ($attribute, $value) {
            if (! is_string($value)) {
                return false;
            }

            return $this->app['math-captcha']->verify($value);
        }, __('Math Captcha Verification Failed!'));
    }

    public function mapParameterToOptions(array $parameters = []): array
    {
        if (! is_array($parameters)) {
            return [];
        }

        $options = [];

        foreach ($parameters as $parameter) {
            $option = explode(':', $parameter);
            if (count($option) === 2) {
                Arr::set($options, $option[0], $option[1]);
            }
        }

        return $options;
    }

    public function provides(): array
    {
        return ['captcha', 'math-captcha'];
    }
}

<?php

namespace Botble\Setting\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Supports\Language;
use Botble\Setting\Http\Requests\GeneralSettingRequest;
use DateTimeZone;
use Illuminate\Support\Facades\App;

class GeneralSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        Assets::usingVueJS()
            ->addStylesDirectly('vendor/core/core/setting/css/admin-email.css')
            ->addScriptsDirectly([
                'vendor/core/core/setting/js/admin-email.js',
                'vendor/core/core/setting/js/license-component.js',
            ]);

        $availableLocales = Language::getAvailableLocales();
        $locales = collect($availableLocales)
            ->pluck('name', 'locale')
            ->map(fn ($item, $key) => $item . ' - ' . $key)
            ->all();

        $this
            ->setSectionTitle(trans('core/setting::setting.general.title'))
            ->setSectionDescription(trans('core/setting::setting.general.description'))
            ->contentOnly()
            ->setValidatorClass(GeneralSettingRequest::class)
            ->add('admin_email', 'html', [
                'html' => view('core/setting::partials.admin-email-field')->render(),
            ])
            ->add(
                'time_zone',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('core/setting::setting.general.time_zone'))
                    ->choices(array_combine(DateTimeZone::listIdentifiers(), DateTimeZone::listIdentifiers()))
                    ->selected(setting('time_zone', 'UTC'))
                    ->searchable()
                    ->toArray()
            )
            ->when(count($locales) > 1, function (FormAbstract $form) use ($locales, $availableLocales) {
                $defaultLocale = setting('locale', App::getLocale());

                if (
                    BaseHelper::hasDemoModeEnabled()
                    && session('site-locale')
                    && array_key_exists(session('site-locale'), $availableLocales)
                ) {
                    $defaultLocale = session('site-locale');
                }

                $form->add(
                    'locale',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(trans('core/setting::setting.general.locale'))
                        ->choices($locales)
                        ->selected($defaultLocale)
                        ->searchable()
                        ->toArray()
                );
            })
            ->add('locale_direction', 'customRadio', [
                'label' => trans('core/setting::setting.general.locale_direction'),
                'value' => setting('locale_direction', 'ltr'),
                'values' => [
                    'ltr' => trans('core/setting::setting.locale_direction_ltr'),
                    'rtl' => trans('core/setting::setting.locale_direction_rtl'),
                ],
            ])
            ->add('enable_send_error_reporting_via_email', 'onOffCheckbox', [
                'label' => trans('core/setting::setting.general.enable_send_error_reporting_via_email'),
                'value' => setting('enable_send_error_reporting_via_email'),
            ])
            ->add('redirect_404_to_homepage', 'onOffCheckbox', [
                'label' => trans('core/setting::setting.general.redirect_404_to_homepage'),
                'value' => setting('redirect_404_to_homepage', false),
                'wrapper' => [
                    'class' => 'mb-0',
                ],
            ])
            ->when(
                apply_filters(BASE_FILTER_AFTER_SETTING_CONTENT, null),
                function (GeneralSettingForm $form, $settingContent) {
                    $form
                        ->add('html', HtmlField::class, [
                            'html' => '</div></div><div class="card mt-3 overflow-hidden"><div class="card-body">' . $settingContent,
                        ]);
                }
            );
    }
}

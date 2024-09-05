<?php

namespace Botble\Analytics\Forms;

use Botble\Analytics\Http\Requests\Settings\AnalyticsSettingRequest;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\Fields\CodeEditorField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Setting\Forms\SettingForm;

class AnalyticsSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        Assets::addScriptsDirectly('vendor/core/plugins/analytics/js/settings.js');

        $this
            ->setSectionTitle(trans('plugins/analytics::analytics.settings.title'))
            ->setSectionDescription(trans('plugins/analytics::analytics.settings.description'))
            ->setFormOption('id', 'google-analytics-settings')
            ->setValidatorClass(AnalyticsSettingRequest::class)
            ->setActionButtons(view('core/setting::forms.partials.action', ['form' => $this->getFormOption('id')])->render())
            ->add('google_analytics', TextField::class, [
                'label' => trans('plugins/analytics::analytics.settings.google_tag_id'),
                'value' => setting('google_analytics'),
                'attr' => [
                    'placeholder' => trans('plugins/analytics::analytics.settings.google_tag_id_placeholder'),
                    'data-counter' => 120,
                ],
                'help_block' => [
                    'text' => sprintf(
                        "<a href='https://support.google.com/analytics/answer/9539598#find-G-ID' target='_blank'>%s</a>",
                        'https://support.google.com/analytics/answer/9539598#find-G-ID'
                    ),
                ],
            ])
            ->add('analytics_property_id', TextField::class, [
                'label' => trans('plugins/analytics::analytics.settings.analytics_property_id'),
                'value' => setting('analytics_property_id'),
                'attr' => [
                    'placeholder' => trans('plugins/analytics::analytics.settings.analytics_property_id_description'),
                    'data-counter' => 9,
                ],
                'help_block' => [
                    'text' => sprintf(
                        "<a href='https://developers.google.com/analytics/devguides/reporting/data/v1/property-id' target='_blank'>%s</a>",
                        'https://developers.google.com/analytics/devguides/reporting/data/v1/property-id'
                    ),
                ],
            ]);

        if (! BaseHelper::hasDemoModeEnabled()) {
            $this
                ->add('analytics_service_account_credentials', CodeEditorField::class, [
                    'label' => trans('plugins/analytics::analytics.settings.json_credential'),
                    'value' => old('analytics_service_account_credentials', setting('analytics_service_account_credentials')),
                    'attr' => [
                        'placeholder' => trans('plugins/analytics::analytics.settings.json_credential_description'),
                        'model' => 'javascript',
                    ],
                    'help_text' => Html::link('https://github.com/akki-io/laravel-google-analytics/wiki/2.-Configure-Google-Service-Account-&-Google-Analytics', attributes: ['target' => '_blank']),
                ])
                ->add('upload_account_json_file', HtmlField::class, HtmlFieldOption::make()->view('plugins/analytics::upload-button')->toArray());
        }

    }
}

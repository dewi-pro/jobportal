<?php

namespace Botble\JobBoard\Forms\Settings;

use Botble\Base\Forms\FormAbstract;
use Botble\JobBoard\Facades\JobBoardHelper;
use Botble\JobBoard\Http\Requests\Settings\GeneralSettingRequest;
use Botble\Setting\Forms\SettingForm;

class GeneralSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/job-board::settings.general.title'))
            ->setSectionDescription(trans('plugins/job-board::settings.general.description'))
            ->setValidatorClass(GeneralSettingRequest::class)
            ->add('job_board_enable_guest_apply', 'onOffCheckbox', [
                'label' => trans('plugins/job-board::settings.general.enable_guest_apply'),
                'value' => JobBoardHelper::isGuestApplyEnabled(),
            ])
            ->add('job_board_enabled_register_as_employer', 'onOffCheckbox', [
                'label' => trans('plugins/job-board::settings.general.enabled_register_as_employer'),
                'value' => setting('job_board_enabled_register_as_employer', true),
            ])
            ->add('verify_account_email', 'onOffCheckbox', [
                'label' => trans('plugins/job-board::settings.general.verify_account_email'),
                'value' => setting('verify_account_email'),
            ])
            ->add('verify_account_created_company', 'onOffCheckbox', [
                'label' => trans('plugins/job-board::settings.general.verify_account_created_company'),
                'value' => setting('verify_account_created_company', true),
            ])
            ->add('job_board_enable_credits_system', 'onOffCheckbox', [
                'label' => trans('plugins/job-board::settings.general.enable_credits_system'),
                'value' => JobBoardHelper::isEnabledCreditsSystem(),
            ])
            ->add('job_board_enable_post_approval', 'onOffCheckbox', [
                'label' => trans('plugins/job-board::settings.general.enable_post_approval'),
                'value' => setting('job_board_enable_post_approval', true),
            ])
            ->add('job_expired_after_days', 'number', [
                'label' => trans('plugins/job-board::settings.general.job_expired_after_days'),
                'value' => JobBoardHelper::jobExpiredDays(),
            ])
            ->add('job_board_job_location_display', 'customRadio', [
                'label' => trans('plugins/job-board::settings.general.job_location_display'),
                'value' => setting('job_board_job_location_display', 'state_and_country'),
                'values' => [
                    'state_and_country' => trans('plugins/job-board::settings.general.state_and_country'),
                    'city_and_state' => trans('plugins/job-board::settings.general.city_and_state'),
                    'city_state_and_country' => trans('plugins/job-board::settings.general.city_state_and_country'),
                ],
            ])
            ->add('job_board_zip_code_enabled', 'onOffCheckbox', [
                'label' => trans('plugins/job-board::settings.general.enable_zip_code'),
                'value' => JobBoardHelper::isZipCodeEnabled(),
            ])
            ->when(is_plugin_active('captcha'), function (FormAbstract $form) {
                $form
                    ->add('openFieldSet', 'html', [
                        'html' => '<fieldset class="form-fieldset">',
                    ])
                    ->add('captchaSettingsTitle', 'html', [
                        'html' => sprintf('<h4>%s</h4>', trans('plugins/captcha::captcha.settings.title')),
                    ])
                    ->when(setting('enable_captcha'), function (FormAbstract $form) {
                        $form
                            ->add('job_board_enable_recaptcha_in_register_page', 'onOffCheckbox', [
                                'label' => trans('plugins/job-board::settings.general.enable_recaptcha_in_register_page'),
                                'value' => setting('job_board_enable_recaptcha_in_register_page', false),
                            ])
                            ->add('job_board_enable_recaptcha_in_apply_job', 'onOffCheckbox', [
                                'label' => trans('plugins/job-board::settings.general.enable_recaptcha_in_apply_job'),
                                'value' => setting('job_board_enable_recaptcha_in_apply_job', false),
                            ]);
                    })
                    ->when(! setting('enable_captcha'), function (FormAbstract $form) {
                        $form->add('captchaDisabledWarning', 'html', [
                            'html' => sprintf('<p class="mb-0 text-muted">%s</p>', trans('plugins/job-board::settings.general.enable_recaptcha_in_pages_description')),
                        ]);
                    })
                    ->add('closeFieldSet', 'html', [
                        'html' => '</fieldset>',
                    ]);
            })
            ->add('job_board_is_enabled_review_feature', 'onOffCheckbox', [
                'label' => trans('plugins/job-board::settings.general.enable_review_feature'),
                'value' => JobBoardHelper::isEnabledReview(),
            ])
            ->add('job_board_disabled_public_profile', 'onOffCheckbox', [
                'label' => trans('plugins/job-board::settings.general.disabled_public_profile'),
                'value' => JobBoardHelper::isDisabledPublicProfile(),
            ])
            ->add('job_board_hide_company_email_enabled', 'onOffCheckbox', [
                'label' => trans('plugins/job-board::settings.general.hide_company_email'),
                'value' => JobBoardHelper::hideCompanyEmailEnabled(),
            ])
            ->add('job_board_default_account_avatar', 'mediaImage', [
                'label' => trans('plugins/job-board::settings.general.default_account_avatar'),
                'value' => setting('job_board_default_account_avatar'),
                'help_block' => [
                    'text' => trans('plugins/job-board::settings.general.default_account_avatar_helper'),
                ],
            ])
            ->add('job_board_enabled_custom_fields_feature', 'onOffCheckbox', [
                'label' => trans('plugins/job-board::settings.general.enable_custom_fields'),
                'value' => JobBoardHelper::isEnabledCustomFields(),
            ])
            ->add('job_board_allow_employer_create_multiple_companies', 'onOffCheckbox', [
                'label' => trans('plugins/job-board::settings.general.allow_employer_multiple_companies'),
                'value' => JobBoardHelper::employerCreateMultipleCompanies(),
            ])
            ->add('job_board_allow_employer_manage_company_info', 'onOffCheckbox', [
                'label' => trans('plugins/job-board::settings.general.allow_employer_manage_company_info'),
                'value' => JobBoardHelper::employerManageCompanyInfo(),
            ]);
    }
}

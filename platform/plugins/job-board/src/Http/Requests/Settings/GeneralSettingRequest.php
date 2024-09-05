<?php

namespace Botble\JobBoard\Http\Requests\Settings;

use Botble\Base\Rules\MediaImageRule;
use Botble\Base\Rules\OnOffRule;
use Botble\Support\Http\Requests\Request;

class GeneralSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'job_board_enable_guest_apply' => [new OnOffRule()],
            'job_board_enabled_register_as_employer' => [new OnOffRule()],
            'verify_account_email' => [new OnOffRule()],
            'verify_account_created_company' => [new OnOffRule()],
            'job_board_enable_credits_system' => [new OnOffRule()],
            'job_board_enable_post_approval' => [new OnOffRule()],
            'job_expired_after_days' => ['required', 'numeric', 'min:1'],
            'job_board_job_location_display' => ['required', 'in:state_and_country,city_and_state,city_state_and_country'],
            'job_board_zip_code_enabled' => [new OnOffRule()],
            'job_board_enable_recaptcha_in_register_page' => [new OnOffRule()],
            'job_board_enable_recaptcha_in_apply_job' => [new OnOffRule()],
            'job_board_is_enabled_review_feature' => [new OnOffRule()],
            'job_board_disabled_public_profile' => [new OnOffRule()],
            'job_board_hide_company_email_enabled' => [new OnOffRule()],
            'job_board_default_account_avatar' => ['nullable', new MediaImageRule()],
            'job_board_enabled_custom_fields_feature' => [new OnOffRule()],
            'job_board_allow_employer_create_multiple_companies' => [new OnOffRule()],
            'job_board_allow_employer_manage_company_info' => [new OnOffRule()],
        ];
    }
}

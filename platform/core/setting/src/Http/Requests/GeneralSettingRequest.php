<?php

namespace Botble\Setting\Http\Requests;

use Botble\Base\Rules\OnOffRule;
use Botble\Base\Supports\Language;
use Botble\Support\Http\Requests\Request;
use DateTimeZone;
use Illuminate\Validation\Rule;

class GeneralSettingRequest extends Request
{
    public function rules(): array
    {
        $onOffRule = new OnOffRule();

        return [
            'admin_email' => ['nullable', 'array'],
            'admin_email.*' => ['nullable', 'email'],
            'time_zone' => Rule::in(DateTimeZone::listIdentifiers()),
            'locale' => ['sometimes', Rule::in(array_keys(Language::getAvailableLocales()))],
            'locale_direction' => ['sometimes', 'in:ltr,rtl'],
            'enable_send_error_reporting_via_email' => [$onOffRule],
            'redirect_404_to_homepage' => [$onOffRule],
        ];
    }
}

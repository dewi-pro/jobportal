<?php

namespace Botble\Contact\Http\Requests;

use Botble\Base\Rules\EmailRule;
use Botble\Base\Rules\PhoneNumberRule;
use Botble\Captcha\Facades\Captcha;
use Botble\Support\Http\Requests\Request;

class ContactRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:40'],
            'email' => ['required', new EmailRule(), 'max:80'],
            'content' => ['required', 'string', 'max:1000'],
            'phone' => ['nullable', new PhoneNumberRule()],
        ];

        if (is_plugin_active('captcha')) {
            $rules += Captcha::rules();

            if (setting('enable_math_captcha_for_contact_form', 0)) {
                $rules += Captcha::mathCaptchaRules();
            }
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => __('Name'),
            'email' => __('Email'),
            'phone' => __('Phone'),
            'content' => __('Content'),
        ] + (is_plugin_active('captcha') ? Captcha::attributes() : []);
    }
}

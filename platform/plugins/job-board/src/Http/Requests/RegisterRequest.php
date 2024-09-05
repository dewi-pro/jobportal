<?php

namespace Botble\JobBoard\Http\Requests;

use Botble\Captcha\Facades\Captcha;
use Botble\JobBoard\Enums\AccountTypeEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class RegisterRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'type' => 'required|' . Rule::in(AccountTypeEnum::values()),
            'first_name' => 'required|max:120|min:2',
            'last_name' => 'required|max:120|min:2',
            'email' => 'required|max:60|min:6|email|unique:jb_accounts',
            'password' => 'required|min:6|confirmed',
        ];

        if (is_plugin_active('captcha')) {
            $rules += Captcha::rules();
        }

        return $rules;
    }

    public function attributes(): array
    {
        return is_plugin_active('captcha') ? Captcha::attributes() : [];
    }
}

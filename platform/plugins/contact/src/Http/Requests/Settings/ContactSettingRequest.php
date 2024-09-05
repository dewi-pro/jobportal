<?php

namespace Botble\Contact\Http\Requests\Settings;

use Botble\Support\Http\Requests\Request;

class ContactSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'blacklist_keywords' => ['nullable', 'string'],
        ];
    }
}

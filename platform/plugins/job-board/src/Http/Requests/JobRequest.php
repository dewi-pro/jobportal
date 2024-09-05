<?php

namespace Botble\JobBoard\Http\Requests;

use Botble\JobBoard\Enums\JobStatusEnum;
use Botble\JobBoard\Enums\ModerationStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class JobRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|min:3|max:255',
            'description' => 'nullable|max:400',
            'status' => Rule::in(JobStatusEnum::values()),
            'moderation_status' => Rule::in(ModerationStatusEnum::values()),
            'latitude' => ['max:20', 'nullable', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => [
                'max:20',
                'nullable',
                'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/',
            ],
            'zip_code' => 'nullable|string|max:20',
            'custom_fields.*.name' => ['required', 'string', 'max:255'],
            'custom_fields.*.value' => ['required', 'string', 'max:255'],
            'number_of_positions' => 'required|integer|max:10000',
        ];
    }
}

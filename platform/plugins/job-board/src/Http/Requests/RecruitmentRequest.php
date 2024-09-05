<?php

namespace Botble\JobBoard\Http\Requests;

use Botble\JobBoard\Enums\JobRecruitmentProgressStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class RecruitmentRequest extends Request
{
    public function rules(): array
    {
        return [
            'catatan' => 'nullable|max:400',
            'status' => Rule::in(JobRecruitmentProgressStatusEnum::values()),
        ];
    }
}

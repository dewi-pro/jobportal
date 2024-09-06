<?php

namespace Botble\JobBoard\Http\Requests;

use Botble\JobBoard\Enums\DatabaseInfoStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class EditDatabaseInfoRequest extends Request
{
    public function rules(): array
    {
        return [
            'status' => Rule::in(DatabaseInfoStatusEnum::values()),
        ];
    }
}

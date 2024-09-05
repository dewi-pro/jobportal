<?php

namespace Botble\Blog\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Rules\MediaImageRule;
use Botble\Blog\Supports\PostFormat;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class PostRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:250'],
            'description' => ['nullable', 'string', 'max:400'],
            'content' => ['nullable', 'string', 'max:300000'],
            'tag' => ['nullable', 'string', 'max:255'],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['sometimes', 'exists:categories,id'],
            'status' => Rule::in(BaseStatusEnum::values()),
            'is_featured' => ['sometimes', 'boolean'],
            'image' => ['nullable', 'string', new MediaImageRule()],
        ];

        $postFormats = PostFormat::getPostFormats(true);

        if (count($postFormats) > 1) {
            $rules['format_type'] = Rule::in(array_keys($postFormats));
        }

        return $rules;
    }
}

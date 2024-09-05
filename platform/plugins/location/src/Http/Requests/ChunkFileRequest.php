<?php

namespace Botble\Location\Http\Requests;

use Botble\Support\Http\Requests\Request;

class ChunkFileRequest extends Request
{
    public function rules(): array
    {
        return [
            'file' => ['required', 'string'],
            'offset' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
        ];
    }
}

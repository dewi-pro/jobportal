<?php

namespace Botble\Location\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Location\Concerns\ChunkFile;
use Botble\Location\Enums\ImportType;
use Botble\Location\Http\Requests\ChunkFileRequest;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ChunkValidateController extends BaseController
{
    use ChunkFile;

    public function __invoke(ChunkFileRequest $request)
    {
        try {
            $filePath = $this->getFilePath($request->input('file'));
        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }

        $offset = $request->integer('offset');
        $limit = $request->integer('limit', 1000);
        $rows = $this->getLocationRows($filePath, $offset, $limit);

        $failed = [];

        $validator = Validator::make($rows, [
            '*.name' => ['required', 'string', 'max:120'],
            '*.slug' => ['nullable', 'string', 'max:120'],
            '*.import_type' => ['required', Rule::in(ImportType::values())],
            '*.order' => ['nullable', 'integer', 'min:0', 'max:127'],
            '*.abbreviation' => ['nullable', 'string', 'max:10'],
            '*.status' => ['required', 'string', Rule::in(BaseStatusEnum::values())],
            '*.country' => ['required_if:import_type,state,city'],
            '*.state' => ['required_if:import_type,city'],
            '*.nationality' => ['nullable', 'string', 'max:120'],
        ]);

        foreach ($validator->errors()->toArray() as $index => $errors) {
            $failed[] = [
                'row' => $index,
                'errors' => $errors,
            ];
        }

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/location::bulk-import.validating_message', [
                'from' => number_format($offset),
                'to' => number_format($offset + count($rows)),
            ]))
            ->setData([
                'offset' => $offset + count($rows),
                'count' => count($rows),
                'failed' => $failed,
            ]);
    }
}

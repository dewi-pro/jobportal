<?php

namespace Botble\Location\Concerns;

use Exception;
use Illuminate\Support\Facades\File;
use Spatie\SimpleExcel\SimpleExcelReader;

trait ChunkFile
{
    protected function getFilePath(string $fileName): string
    {
        $filePath = storage_path("app/location-import/$fileName");

        if (! File::exists($filePath)) {
            throw new Exception(__('Your file is not found. Please try uploading again.'));
        }

        return $filePath;
    }

    protected function getLocationRows(string $filePath, int $offset = 0, int $limit = 1000): array
    {
        return SimpleExcelReader::create($filePath)
            ->headersToSnakeCase()
            ->skip($offset)
            ->take($limit)
            ->getRows()
            ->toArray();
    }
}

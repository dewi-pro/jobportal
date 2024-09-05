<?php

namespace Botble\Location\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Media\Chunks\Exceptions\UploadMissingFileException;
use Botble\Media\Chunks\Handler\DropZoneUploadHandler;
use Botble\Media\Chunks\Receiver\FileReceiver;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChunkUploadController extends BaseController
{
    public function __invoke(Request $request): BaseHttpResponse
    {
        $receiver = new FileReceiver('file', $request, DropZoneUploadHandler::class);

        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();

        if (! $save->isFinished()) {
            $handler = $save->handler();

            return $this
                ->httpResponse()
                ->setData([
                    'done' => $handler->getPercentageDone(),
                    'status' => true,
                ]);
        }

        $sessionId = $request->input('dzuuid', Str::uuid());
        $file = $save->getFile();
        $filePath = sprintf('%s.%s', $sessionId, $file->getClientOriginalExtension());
        $file->move(storage_path('app/location-import'), $filePath);

        return $this
            ->httpResponse()
            ->setData([
                'file_path' => $filePath,
            ]);
    }
}

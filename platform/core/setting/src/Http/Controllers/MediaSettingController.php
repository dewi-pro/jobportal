<?php

namespace Botble\Setting\Http\Controllers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Media\Facades\RvMedia;
use Botble\Media\Models\MediaFile;
use Botble\Setting\Forms\MediaSettingForm;
use Botble\Setting\Http\Requests\MediaSettingRequest;
use Exception;

class MediaSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('core/setting::setting.media.title'));

        $form =  MediaSettingForm::create();

        return view('core/setting::media', compact('form'));
    }

    public function update(MediaSettingRequest $request): BaseHttpResponse
    {
        return $this->performUpdate([
            ...$request->validated(),
            'media_folders_can_add_watermark' => $request->boolean('media_folders_can_add_watermark_all')
                ? []
                : $request->input('media_folders_can_add_watermark', []),
        ]);
    }

    public function generateThumbnails(): BaseHttpResponse
    {
        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        $files = MediaFile::query()->select(['url', 'mime_type', 'folder_id'])->get();

        $errors = [];

        if ($files->isNotEmpty()) {
            foreach ($files as $file) {
                try {
                    /**
                     * @var MediaFile $file
                     */
                    RvMedia::generateThumbnails($file);
                } catch (Exception) {
                    $errors[] = $file->url;
                }
            }

            $errors = array_unique($errors);

            $errors = array_map(function ($item) {
                return [$item];
            }, $errors);
        }

        if ($errors) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('core/setting::setting.generate_thumbnails_error', ['count' => count($errors)]));
        }

        return $this
            ->httpResponse()
            ->setMessage(trans('core/setting::setting.generate_thumbnails_success', ['count' => count($files)]));
    }
}

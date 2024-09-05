<?php

namespace Botble\Captcha\Http\Controllers\Settings;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Captcha\Forms\CaptchaSettingForm;
use Botble\Captcha\Http\Requests\Settings\CaptchaSettingRequest;
use Botble\Setting\Http\Controllers\SettingController;

class CaptchaSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/captcha::captcha.settings.title'));

        return CaptchaSettingForm::create()->renderForm();
    }

    public function update(CaptchaSettingRequest $request): BaseHttpResponse
    {
        return $this->performUpdate($request->validated());
    }
}

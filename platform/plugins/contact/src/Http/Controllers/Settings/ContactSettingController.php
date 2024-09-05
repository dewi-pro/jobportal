<?php

namespace Botble\Contact\Http\Controllers\Settings;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Contact\Forms\Settings\ContactSettingForm;
use Botble\Contact\Http\Requests\Settings\ContactSettingRequest;
use Botble\Setting\Http\Controllers\SettingController;

class ContactSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/contact::contact.settings.title'));

        return ContactSettingForm::create()->renderForm();
    }

    public function update(ContactSettingRequest $request): BaseHttpResponse
    {
        return $this->performUpdate($request->validated());
    }
}

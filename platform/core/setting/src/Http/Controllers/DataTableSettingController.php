<?php

namespace Botble\Setting\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Forms\DataTableSettingForm;
use Botble\Setting\Http\Requests\DataTableSettingRequest;

class DataTableSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('core/setting::setting.datatable.title'));

        return DataTableSettingForm::create()->renderForm();
    }

    public function update(DataTableSettingRequest $request): BaseHttpResponse
    {
        return $this->performUpdate($request->validated());
    }
}

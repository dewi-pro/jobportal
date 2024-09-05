<?php

namespace Botble\Setting\Http\Controllers;

use Botble\Base\Facades\Assets;
use Illuminate\Contracts\View\View;

class EmailTemplateSettingController extends SettingController
{
    public function index(): View
    {
        $this->pageTitle(trans('core/setting::setting.email.email_templates'));

        Assets::addScriptsDirectly('vendor/core/core/setting/js/email-template.js');

        return view('core/setting::email-templates');
    }
}

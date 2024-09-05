<?php

namespace Botble\Setting\Http\Controllers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Setting\Http\Traits\GetEmailTemplateDataTrait;
use Illuminate\Http\Request;

class EmailTemplateIframeController extends BaseController
{
    use GetEmailTemplateDataTrait;

    public function __invoke(Request $request, string $type, string $module, string $template)
    {
        [$inputData, $variables, $emailHandler] = $this->getData($request, $type, $module, $template);

        foreach ($variables as $key => $variable) {
            if (! isset($inputData[$key])) {
                $inputData[$key] = '{{ ' . $key . ' }}';
            } else {
                $inputData[$key] = BaseHelper::clean(BaseHelper::stringify($inputData[$key]));
            }
        }

        $emailHandler->setVariableValues($inputData);

        $content = get_setting_email_template_content($type, $module, $template);

        $content = $emailHandler->prepareData($content);

        return BaseHelper::clean($content);
    }
}

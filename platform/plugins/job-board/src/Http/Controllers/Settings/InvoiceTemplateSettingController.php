<?php

namespace Botble\JobBoard\Http\Controllers\Settings;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\JobBoard\Http\Requests\Settings\InvoiceTemplateSettingRequest;
use Botble\JobBoard\Supports\InvoiceHelper;
use Botble\Setting\Http\Controllers\SettingController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class InvoiceTemplateSettingController extends SettingController
{
    public function edit(InvoiceHelper $invoiceHelper)
    {
        Assets::addScriptsDirectly('vendor/core/core/setting/js/setting.js');

        $content = $invoiceHelper->getInvoiceTemplate();
        $variables = $invoiceHelper->getVariables();

        return view('plugins/job-board::invoice-template.edit', compact('content', 'variables'));
    }

    public function update(InvoiceTemplateSettingRequest $request): BaseHttpResponse
    {
        BaseHelper::saveFileData(storage_path('app/templates/invoice.tpl'), $request->input('content'), false);

        return $this
            ->httpResponse()
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function reset(): BaseHttpResponse
    {
        File::delete(storage_path('app/templates/invoice.tpl'));

        return $this
            ->httpResponse()
            ->setMessage(trans('core/setting::setting.email.reset_success'));
    }

    public function preview(InvoiceHelper $invoiceHelper): Response
    {
        $invoice = $invoiceHelper->getDataForPreview();

        return $invoiceHelper->streamInvoice($invoice);
    }
}

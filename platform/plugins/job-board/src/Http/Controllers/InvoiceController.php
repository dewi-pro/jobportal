<?php

namespace Botble\JobBoard\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\JobBoard\Models\Invoice;
use Botble\JobBoard\Supports\InvoiceHelper;
use Botble\JobBoard\Tables\InvoiceTable;
use Exception;
use Illuminate\Http\Request;

class InvoiceController extends BaseController
{
    public function index(InvoiceTable $table)
    {
        $this->pageTitle(trans('plugins/job-board::invoice.name'));

        return $table->renderTable();
    }

    public function edit(Invoice $invoice, Request $request)
    {
        event(new BeforeEditContentEvent($request, $invoice));

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $invoice->code]));

        Assets::addStylesDirectly('vendor/core/plugins/job-board/css/invoice.css');

        return view('plugins/job-board::invoice.edit', ['invoice' => $invoice]);
    }

    public function destroy(Invoice $invoice, Request $request)
    {
        try {
            $invoice->delete();

            event(new DeletedContentEvent(INVOICE_MODULE_SCREEN_NAME, $request, $invoice));

            return $this
                ->httpResponse()
                ->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function getGenerateInvoice(int $invoiceId, Request $request, InvoiceHelper $invoiceHelper)
    {
        /** @var \Botble\JobBoard\Models\Invoice $invoice */
        $invoice = Invoice::query()->findOrFail($invoiceId);

        if ($request->input('type') === 'print') {
            return $invoiceHelper->streamInvoice($invoice);
        }

        return $invoiceHelper->downloadInvoice($invoice);
    }
}

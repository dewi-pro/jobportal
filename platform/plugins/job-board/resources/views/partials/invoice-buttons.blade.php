<a
    class="btn btn-success my-2"
    href="{{ route('invoice.generate-invoice', ['id' => $invoice->id, 'type' => 'print']) }}"
    target="_blank"
>
    {{ trans('plugins/job-board::invoice.print') }}
</a>

<a
    class="btn btn-danger my-2"
    href="{{ route('invoice.generate-invoice', ['id' => $invoice->id, 'type' => 'download']) }}"
    target="_blank"
>
    {{ trans('plugins/job-board::invoice.download') }}
</a>

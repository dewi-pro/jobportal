<li class="list-group-item">
    <input
        class="magic-radio js_payment_method"
        id="payment_bank_transfer"
        name="payment_method"
        type="radio"
        value="bank_transfer"
        @if (PaymentMethods::getSelectingMethod() == Botble\Payment\Enums\PaymentMethodEnum::BANK_TRANSFER) checked @endif
    >
    <label class="text-start" for="payment_bank_transfer">
        {{ get_payment_setting('name', 'bank_transfer', trans('plugins/payment::payment.payment_via_bank_transfer')) }}
    </label>
    <div
        class="payment_bank_transfer_wrap payment_collapse_wrap collapse @if (PaymentMethods::getSelectingMethod() == Botble\Payment\Enums\PaymentMethodEnum::BANK_TRANSFER) show @endif"
        style="padding: 15px 0;"
    >
        <div class="ck-content">
            {!! BaseHelper::clean(get_payment_setting('description', 'bank_transfer')) !!}
        </div>
    </div>
</li>

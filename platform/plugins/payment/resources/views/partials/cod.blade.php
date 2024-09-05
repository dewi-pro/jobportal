<li class="list-group-item">
    <input
        class="magic-radio js_payment_method"
        id="payment_cod"
        name="payment_method"
        type="radio"
        value="cod"
        @if (PaymentMethods::getSelectingMethod() == Botble\Payment\Enums\PaymentMethodEnum::COD) checked @endif
    >
    <label class="text-start" for="payment_cod">
        {{ get_payment_setting('name', 'cod', trans('plugins/payment::payment.payment_via_cod')) }}
    </label>
    <div
        class="payment_cod_wrap payment_collapse_wrap collapse @if (PaymentMethods::getSelectingMethod() == Botble\Payment\Enums\PaymentMethodEnum::COD) show @endif"
        style="padding: 15px 0;"
    >
        {!! BaseHelper::clean(get_payment_setting('description', 'cod')) !!}
    </div>
</li>

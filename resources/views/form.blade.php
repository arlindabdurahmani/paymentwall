<div class="payment-form">
    <h3>{{ trans('plugins/paymentwall::paymentwall.payment_description') }}</h3>
    <form action="{{ route('paymentwall.pay') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary">
            {{ trans('plugins/paymentwall::paymentwall.pay_now') }}
        </button>
    </form>
</div>

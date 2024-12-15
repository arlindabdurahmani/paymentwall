<div class="payment-detail">
    <h3>{{ trans('plugins/paymentwall::paymentwall.transaction_details') }}</h3>
    <p><strong>Transaction ID:</strong> {{ $transaction->id }}</p>
    <p><strong>Amount:</strong> ${{ number_format($transaction->amount, 2) }}</p>
    <p><strong>Status:</strong> {{ $transaction->status }}</p>
    <p><strong>Date:</strong> {{ $transaction->created_at->format('Y-m-d H:i:s') }}</p>
</div>
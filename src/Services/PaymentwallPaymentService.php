<?php

namespace Botble\Paymentwall\Services;

class PaymentwallPaymentService
{
    /**
     * Check if online refunds are supported.
     *
     * @return bool
     */
    public function getSupportRefundOnline(): bool
    {
        return true;
    }

    /**
     * Process a refund for an order.
     *
     * @param string $transactionId
     * @param float $amount
     * @return array
     */
    public function refundOrder(string $transactionId, float $amount): array
    {
        return (new PaymentwallService())->refundOrder($transactionId, $amount);
    }

    /**
     * Get payment details using the Paymentwall service.
     *
     * @param string $transactionId
     * @return array|null
     */
    public function getPaymentDetails(string $transactionId): ?array
    {
        return (new PaymentwallService())->queryTransaction($transactionId);
    }
}

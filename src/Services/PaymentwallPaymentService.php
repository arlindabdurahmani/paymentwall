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
     * @param string $paymentId
     * @param float $amount
     * @return array
     */
    public function refundOrder(string $paymentId, float $amount): array
    {
        return (new PaymentwallService())->refundOrder($paymentId, $amount);
    }

    /**
     * Get payment details using the Paymentwall service.
     *
     * @param string $paymentId
     * @return array|null
     */
    public function getPaymentDetails(string $paymentId): ?array
    {
        return (new PaymentwallService())->queryTransaction($paymentId);
    }
}

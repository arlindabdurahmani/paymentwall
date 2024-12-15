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
     * @return mixed
     */
    public function refundOrder(string $paymentId, float $amount)
    {
        return (new PaymentwallService())->refundOrder($paymentId, $amount);
    }

    /**
     * Get payment details using the Paymentwall service.
     *
     * @param string $paymentId
     * @return mixed
     */
    public function getPaymentDetails(string $paymentId)
    {
        return (new PaymentwallService())->queryTransaction($paymentId);
    }
}

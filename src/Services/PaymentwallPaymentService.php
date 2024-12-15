<?php

namespace Botble\Paymentwall\Services;

use Paymentwall_Widget;
use Paymentwall_Product;

class PaymentwallPaymentService
{
    /**
     * Create a Paymentwall widget for payment.
     *
     * @param string $userId
     * @param array $products
     * @param string $callbackUrl
     * @return Paymentwall_Widget
     */
    public function createWidget($userId, array $products, $callbackUrl)
    {
        // Initialize the API
        PaymentwallService::initialize();

        // Create the widget
        return new Paymentwall_Widget(
            $userId, // User ID
            'p1', // Widget Code
            $products, // Products for purchase
            [
                'success_url' => $callbackUrl, // Callback URL for success
                'cancel_url' => url()->previous(), // Cancel URL
            ]
        );
    }

    /**
     * Create a product for Paymentwall widget.
     *
     * @param string $id
     * @param float $amount
     * @param string $currency
     * @param string $description
     * @return Paymentwall_Product
     */
    public function createProduct($id, $amount, $currency, $description)
    {
        return new Paymentwall_Product(
            $id, // Product ID
            $amount, // Price
            $currency, // Currency (e.g., USD)
            $description, // Product description
            Paymentwall_Product::TYPE_FIXED // Product type
        );
    }
}

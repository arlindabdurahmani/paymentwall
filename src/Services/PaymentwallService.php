<?php

namespace Botble\Paymentwall\Services;

use Botble\Paymentwall\Providers\PaymentwallServiceProvider;

class PaymentwallService
{
    protected string $publicKey;
    protected string $secretKey;
    protected array $data = [];

    public function __construct()
    {
        $this->publicKey = get_payment_setting('public_key', PaymentwallServiceProvider::MODULE_NAME);
        $this->secretKey = get_payment_setting('secret_key', PaymentwallServiceProvider::MODULE_NAME);
    }

    public function withData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function redirectToCheckoutPage(): void
    {
        $widgetUrl = $this->getWidgetUrl();

        echo view('plugins/paymentwall::form', [
            'action' => $widgetUrl,
            'data' => [],
        ]);

        exit();
    }

    protected function getWidgetUrl(): string
    {
        $params = [
            'key' => $this->publicKey,
            'uid' => $this->data['customer_id'] ?? 'guest', // Replace with actual user ID
            'goodsid' => $this->data['meta']['order_id'], // Product or order ID
            'amount' => $this->data['amount'],
            'currency' => $this->data['currency'],
            'success_url' => route('payment.paymentwall.callback'),
            'cancel_url' => PaymentHelper::getCancelURL(),
        ];

        return 'https://www.paymentwall.com/api/ps/?' . http_build_query($params);
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }
}

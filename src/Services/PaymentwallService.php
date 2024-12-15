<?php

namespace Botble\Paymentwall\Services;

use Botble\Paymentwall\Providers\PaymentwallServiceProvider;
use Illuminate\Support\Facades\Http;

class PaymentwallService
{
    protected string $publicKey;

    protected string $secretKey;

    protected string $baseApiUrl = 'https://api.paymentwall.com/api';

    protected array $data = [];

    public function __construct()
    {
        $this->publicKey = get_payment_setting('public_key', PaymentwallServiceProvider::MODULE_NAME);
        $this->secretKey = get_payment_setting('secret_key', PaymentwallServiceProvider::MODULE_NAME);
    }

    public function withData(array $data): self
    {
        $this->data = $data;

        $this->withAdditionalData();

        return $this;
    }

    public function redirectToCheckoutPage(): void
    {
        echo view('plugins/paymentwall::form', [
            'data' => $this->data,
            'action' => $this->getPaymentUrl(),
        ]);

        exit();
    }

    protected function getPaymentUrl(): string
    {
        return $this->baseApiUrl . '/payment';
    }

    protected function withAdditionalData(): void
    {
        $this->data = array_merge($this->data, [
            'key' => $this->getPublicKey(),
        ]);
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    public function queryTransaction(string $referenceNumber)
    {
        $response = Http::asJson()
            ->withHeaders(['Authorization' => $this->getSecretKey()])
            ->withoutVerifying()
            ->get($this->baseApiUrl . '/rest/transactions/' . $referenceNumber);

        if (! $response->ok()) {
            return [
                'error' => true,
                'message' => $response->reason(),
            ];
        }

        return $response->json();
    }

    public function refundOrder(string $chargeId, float $amount): array
    {
        $response = Http::asJson()
            ->withHeaders(['Authorization' => $this->getSecretKey()])
            ->withoutVerifying()
            ->post($this->baseApiUrl . '/rest/transactions/' . $chargeId . '/refund', [
                'amount' => $amount,
            ]);

        if ($response->status() == 400) {
            return [
                'error' => true,
                'message' => __('Refunds are not enabled by default on Paymentwall accounts. You may need to contact Paymentwall support to enable this feature.'),
            ];
        }

        return [
            'error' => $response->failed(),
            'message' => $response->json('message'),
        ];
    }
}

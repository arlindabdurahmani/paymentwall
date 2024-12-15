<?php

namespace Botble\Paymentwall\Services;

use Botble\Paymentwall\Providers\PaymentwallServiceProvider;
use Illuminate\Support\Facades\Http;

class PaymentwallService
{
    protected string $projectKey;
    protected string $secretKey;
    protected string $baseWidgetUrl = 'https://www.paymentwall.com/api/ps';
    protected string $baseApiUrl = 'https://api.paymentwall.com/api/rest';
    protected array $data = [];

    public function __construct()
    {
        $this->projectKey = get_payment_setting('project_key', PaymentwallServiceProvider::MODULE_NAME);
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
            'key' => $this->projectKey,
            'uid' => $this->data['customer_id'] ?? 'guest',
            'goodsid' => $this->data['meta']['order_id'] ?? 'unknown',
            'amount' => $this->data['amount'],
            'currency' => $this->data['currency'],
            'success_url' => route('payment.paymentwall.callback'),
            'cancel_url' => PaymentHelper::getCancelURL(),
        ];

        return $this->baseWidgetUrl . '/?' . http_build_query($params);
    }

    public function getProjectKey(): string
    {
        return $this->projectKey;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    public function queryTransaction(string $transactionId)
    {
        $response = Http::asJson()
            ->withHeaders(['Authorization' => $this->secretKey])
            ->withoutVerifying()
            ->get($this->baseApiUrl . '/transactions/' . $transactionId);

        if (!$response->ok()) {
            return [
                'error' => true,
                'message' => $response->reason(),
            ];
        }

        return $response->json();
    }

    public function refundOrder(string $transactionId, float $amount): array
    {
        $response = Http::asJson()
            ->withHeaders(['Authorization' => $this->secretKey])
            ->withoutVerifying()
            ->post($this->baseApiUrl . '/transactions/' . $transactionId . '/refund', [
                'amount' => $amount,
            ]);

        if ($response->status() == 400) {
            return [
                'error' => true,
                'message' => __('Refunds are not enabled on your Paymentwall account by default. Contact Paymentwall support to enable this feature.'),
            ];
        }

        return [
            'error' => $response->failed(),
            'message' => $response->json('message'),
        ];
    }
}

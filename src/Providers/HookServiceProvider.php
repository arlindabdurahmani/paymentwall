<?php

namespace Botble\Paymentwall\Providers;

use Botble\Ecommerce\Facades\OrderHelper;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Facades\PaymentMethods;
use Botble\Paymentwall\Services\PaymentwallService;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(PAYMENT_METHODS_SETTINGS_PAGE, function (?string $settings) {
            $name = 'Paymentwall';
            $moduleName = PaymentwallServiceProvider::MODULE_NAME;
            $status = (bool) get_payment_setting('status', $moduleName);

            return $settings . view('plugins/paymentwall::settings', compact('name', 'moduleName', 'status'))->render();
        }, 999);

        add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, function (?string $html, array $data): ?string {
            if (get_payment_setting('status', PaymentwallServiceProvider::MODULE_NAME)) {
                $paymentwallService = new PaymentwallService();

                if (!$paymentwallService->getProjectKey() || !$paymentwallService->getSecretKey()) {
                    return $html;
                }

                PaymentMethods::method(PaymentwallServiceProvider::MODULE_NAME, [
                    'html' => view(
                        'plugins/paymentwall::methods',
                        $data,
                        ['moduleName' => PaymentwallServiceProvider::MODULE_NAME]
                    )->render(),
                ]);
            }

            return $html;
        }, 999, 2);

        add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, function (array $data, Request $request): array {
            if ($data['type'] !== PaymentwallServiceProvider::MODULE_NAME) {
                return $data;
            }

            $paymentData = apply_filters(PAYMENT_FILTER_PAYMENT_DATA, [], $request);

            try {
                $paymentwallService = new PaymentwallService();

                $data = [
                    'project_key' => $paymentwallService->getProjectKey(),
                    'redirect_url' => route('payment.paymentwall.callback'),
                    'currency' => $data['currency'],
                    'amount' => $data['amount'],
                    'customer[email]' => $paymentData['address']['email'],
                    'customer[name]' => $paymentData['address']['name'],
                    'meta' => [
                        'order_id' => json_encode($paymentData['order_id']),
                        'customer_id' => $paymentData['customer_id'],
                        'customer_type' => $paymentData['customer_type'],
                    ],
                ];

                $paymentwallService->withData($data)->redirectToCheckoutPage();
            } catch (\Throwable $exception) {
                $data['error'] = true;
                $data['message'] = $exception->getMessage();
            }

            return $data;
        }, 999, 2);
    }
}

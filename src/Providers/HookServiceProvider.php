<?php

namespace Botble\Paymentwall\Providers;

use Botble\Ecommerce\Facades\OrderHelper;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Facades\PaymentMethods;
use Botble\Paymentwall\Services\PaymentwallPaymentService;
use Botble\Paymentwall\Services\PaymentwallService;
use Html;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Throwable;

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

        add_filter(BASE_FILTER_ENUM_ARRAY, function (array $values, string $class): array {
            if ($class === PaymentMethodEnum::class) {
                $values['paymentwall'] = PaymentwallServiceProvider::MODULE_NAME;
            }

            return $values;
        }, 999, 2);

        add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class): string {
            if ($class === PaymentMethodEnum::class && $value === PaymentwallServiceProvider::MODULE_NAME) {
                $value = 'Paymentwall';
            }

            return $value;
        }, 999, 2);

        add_filter(BASE_FILTER_ENUM_HTML, function (string $value, string $class): string {
            if ($class === PaymentMethodEnum::class && $value === PaymentwallServiceProvider::MODULE_NAME) {
                $value = Html::tag(
                    'span',
                    PaymentMethodEnum::getLabel($value),
                    ['class' => 'label-success status-label']
                )->toHtml();
            }

            return $value;
        }, 999, 2);

        add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, function (?string $html, array $data): ?string {
            if (get_payment_setting('status', PaymentwallServiceProvider::MODULE_NAME)) {
                $payPaymentwall = new PaymentwallService();

                if (! $payPaymentwall->getPublicKey() || ! $payPaymentwall->getSecretKey()) {
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

        add_filter(PAYMENT_FILTER_PAYMENT_INFO_DETAIL, function ($data, $payment) {
            if ($payment->payment_channel == PaymentwallServiceProvider::MODULE_NAME) {
                $paymentDetail = (new PaymentwallPaymentService())->getPaymentDetails($payment->charge_id);

                $data = view('plugins/paymentwall::detail', ['payment' => $paymentDetail])->render();
            }

            return $data;
        }, 1, 2);

        add_filter(PAYMENT_FILTER_GET_SERVICE_CLASS, function (?string $data, string $value): ?string {
            if ($value === PaymentwallServiceProvider::MODULE_NAME) {
                $data = PaymentwallPaymentService::class;
            }

            return $data;
        }, 20, 2);

        add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, function (array $data, Request $request): array {
            if ($data['type'] !== PaymentwallServiceProvider::MODULE_NAME) {
                return $data;
            }

            $paymentData = apply_filters(PAYMENT_FILTER_PAYMENT_DATA, [], $request);

            try {
                $payPaymentwall = new PaymentwallService();

                $data = [
                    'public_key' => $payPaymentwall->getPublicKey(),
                    'redirect_url' => route('payment.paymentwall.callback'),
                    'currency' => $data['currency'],
                    'amount' => $data['amount'],
                    'customer[email]' => $paymentData['address']['email'],
                    'customer[name]' => $paymentData['address']['name'],
                    'meta[customer_id]' => $paymentData['customer_id'],
                    'meta[customer_type]' => $paymentData['customer_type'],
                    'meta[order_id]' => json_encode($paymentData['order_id']),
                ];

                if (is_plugin_active('ecommerce')) {
                    $data['tx_ref'] = OrderHelper::getOrderSessionToken() . '-' . time();
                    $data['meta[token]'] = OrderHelper::getOrderSessionToken();
                } else {
                    $tokenGenerate = session('subscribed_packaged_id');
                    $data['tx_ref'] = $tokenGenerate . '-' . time();
                    $data['meta[token]'] = $tokenGenerate;
                }

                $payPaymentwall->withData($data);

                $payPaymentwall->redirectToCheckoutPage();
            } catch (Throwable $exception) {
                $data['error'] = true;
                $data['message'] = json_encode($exception->getMessage());
            }

            return $data;
        }, 999, 2);
    }
}

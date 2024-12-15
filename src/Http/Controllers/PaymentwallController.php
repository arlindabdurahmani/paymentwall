<?php

namespace Botble\Paymentwall\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Customer;
use Botble\Hotel\Models\Booking;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Supports\PaymentHelper;
use Botble\Paymentwall\Providers\PaymentwallServiceProvider;
use Botble\Paymentwall\Services\PaymentwallService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PaymentwallController extends BaseController
{
    public function callback(
        Request $request,
        BaseHttpResponse $response,
        PaymentwallService $paymentwallService
    ): BaseHttpResponse {
        if (! $request->input('transaction_id')) {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL());
        }

        $result = $paymentwallService->queryTransaction($request->input('transaction_id'));

        if (! $result) {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL());
        }

        $data = $result['data'];

        switch ($data['status']) {
            case 'successful':
                $status = PaymentStatusEnum::COMPLETED;
                break;

            case 'failure':
                $status = PaymentStatusEnum::FAILED;
                break;

            default:
                $status = PaymentStatusEnum::PENDING;
                break;
        }

        if ($status === PaymentStatusEnum::FAILED) {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->setMessage($request->input('error_message'));
        }

        do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
            'order_id' => $orderId = json_decode($data['meta']['order_id']),
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'charge_id' => $data['id'],
            'payment_channel' => PaymentwallServiceProvider::MODULE_NAME,
            'status' => $status,
            'customer_id' => $data['meta']['customer_id'],
            'customer_type' => $data['meta']['customer_type'],
            'payment_type' => 'direct',
        ], $request);

        if (is_plugin_active('hotel')) {
            $booking = Booking::query()
                ->select('transaction_id')
                ->find(Arr::first($orderId));

            if (! $booking) {
                return $response
                    ->setNextUrl(PaymentHelper::getCancelURL())
                    ->setMessage(__('Checkout failed!'));
            }

            return $response
                ->setNextUrl(PaymentHelper::getRedirectURL($booking->transaction_id))
                ->setMessage(__('Checkout successfully!'));
        }

        $nextUrl = PaymentHelper::getRedirectURL($data['meta']['token']);

        if (is_plugin_active('job-board') || is_plugin_active('real-estate')) {
            $nextUrl = $nextUrl . '?charge_id=' . $data['id'];
        }

        return $response
            ->setNextUrl($nextUrl)
            ->setMessage(__('Checkout successfully!'));
    }

    public function webhook(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (! $data) {
            return;
        }

        $status = match ($data['status']) {
            'successful' => PaymentStatusEnum::COMPLETED,
            'failure' => PaymentStatusEnum::FAILED,
            default => PaymentStatusEnum::PENDING,
        };

        if ($status === PaymentStatusEnum::FAILED) {
            return;
        }

        do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
            'order_id' => json_decode($data['meta']['order_id']),
            'amount' => $data['amount'],
            'charge_id' => $data['id'],
            'payment_channel' => PaymentwallServiceProvider::MODULE_NAME,
            'status' => $status,
            'customer_id' => $data['customer']['id'],
            'customer_type' => Customer::class,
            'payment_type' => 'direct',
        ], $request);
    }
}

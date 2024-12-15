<?php

namespace Botble\Paymentwall\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Paymentwall_Base;
use Paymentwall_Product;
use Paymentwall_Widget;

class PaymentwallController extends Controller
{
    /**
     * Initiate a Paymentwall payment
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pay(Request $request)
    {
        // Configure Paymentwall API
        Paymentwall_Base::setApiType(Paymentwall_Base::API_GOODS);
        Paymentwall_Base::setAppKey(env('PAYMENTWALL_PUBLIC_KEY'));
        Paymentwall_Base::setSecretKey(env('PAYMENTWALL_SECRET_KEY'));

        // Create a Paymentwall widget
        $widget = new Paymentwall_Widget(
            $request->user()->id, // User ID
            'p1', // Widget Code
            [
                new Paymentwall_Product(
                    'product_id',
                    $request->input('amount', 10.00), // Amount
                    $request->input('currency', 'USD'), // Currency
                    $request->input('description', 'Sample Product'), // Description
                    Paymentwall_Product::TYPE_FIXED // Product Type
                ),
            ],
            [
                'success_url' => route('paymentwall.callback'),
                'cancel_url' => url()->previous(),
            ]
        );

        // Redirect to the Paymentwall widget URL
        return redirect($widget->getUrl());
    }

    /**
     * Handle Paymentwall callback
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function callback(Request $request)
    {
        // Paymentwall Pingback verification
        $pingback = new \Paymentwall_Pingback($request->all(), $request->ip());

        if ($pingback->validate()) {
            if ($pingback->isDeliverable()) {
                // Handle successful transaction (e.g., update order status)
                return response()->json(['status' => 'success', 'message' => 'Payment successful!']);
            } elseif ($pingback->isCancelable()) {
                // Handle canceled transaction
                return response()->json(['status' => 'canceled', 'message' => 'Payment canceled.']);
            }
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid callback.'], 400);
    }
}

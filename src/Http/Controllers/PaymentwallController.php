<?php

namespace Botble\Paymentwall\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentwallController extends Controller
{
    public function handleCallback(Request $request)
    {
        // Handle the callback from Paymentwall
        return response()->json(['message' => 'Callback received successfully.']);
    }
}

<?php
namespace orders\api\controllers;

use shared\events\CheckoutCompleted;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\Request;
use orders\application\commands\OrderCheckoutHandler;

class CheckoutController
{
    public function checkout( Request $request, OrderCheckoutHandler $orderCheckoutHandler)
    {
        $paymentUrl = $orderCheckoutHandler->handle($request->user()) ;
        \Log::info('Payment URL: ' . $paymentUrl);
        return response()->json([
            'success' => true,
            'message' => 'Redirecting...',
            'payment_url' => $paymentUrl,
        ]);
    }
}

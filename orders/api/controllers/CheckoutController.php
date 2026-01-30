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
        $result = $orderCheckoutHandler->handle($request->user() , $request->all());
        if (!($result['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Checkout failed.',
                'missing_fields' => $result['missing_fields'] ?? []
            ], 400);
        }
        $paymentUrl = $result['payment_url'] ?? null;
        \Log::info('Payment URL: ' . $paymentUrl);
        return response()->json([
            'success' => true,
            'message' => 'Redirecting...',
            'payment_url' => $paymentUrl,
        ]);
    }
}

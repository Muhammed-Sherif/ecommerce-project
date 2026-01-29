<?php
namespace payments\api\controllers;

use Illuminate\Http\Request;
use payments\application\commands\PaymentWebhookHandler;

class PaymentController
{
    private $webhookHandler;

    public function __construct(PaymentWebhookHandler $webhookHandler)
    {
        $this->webhookHandler = $webhookHandler;
    }

    public function webhook(string $gateway, Request $request)
    {
        $data = $request->all();

        $result = $this->webhookHandler->handle($gateway, $data);

        return response()->json($result);
    }
}

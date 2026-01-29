<?php
namespace payments\domains\models;

class Payment
{
    public $id;
    public $orderId;
    public $amount;
    public $currency;
    public $status;
    public $transactionId;
    public $paymentUrl;
    public $createdAt;

    public function __construct(
        $id,
        $orderId,
        $amount,
        $currency,
        string $status = 'pending',
        string $transactionId = null,
        string $paymentUrl = null,
        $createdAt = null
    ) {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->status = $status;
        $this->transactionId = $transactionId;
        $this->paymentUrl = $paymentUrl;
        $this->createdAt = $createdAt;
    }
}

<?php
namespace payments\infrastructure\repositories;

use payments\domains\contracts\IPaymentRepository;
use App\Models\Payment;

class PaymentRepository implements IPaymentRepository
{
    public function create(array $paymentData)
    {
        $payment = Payment::query()->create($paymentData);
        return $payment->id;
    }

    public function findByOrderId($orderId)
    {
        $query = Payment::query()->where('order_id', $orderId);
        return $query->first();
    }

    public function update($id, array $data)
    {
        $query = Payment::query()->where('id', $id);
        return $query->update($data);
    }
    
    // Helper to find by temporary lookup if needed
    public function findByCheckoutId($checkoutId)
    {
        // This assumes we stored checkout_id on the order and joined, or passed it through.
        // For simplicity in this demo, we might rely on the return of the event dispatch if meaningful
        // or just find the latest payment for the user (passed in context).
        return null; 
    }
}

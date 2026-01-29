<?php
namespace payments\infrastructure\repositories;

use payments\domains\contracts\IPaymentRepository;
use Illuminate\Support\Facades\DB;

class PaymentRepository implements IPaymentRepository
{
    public function create(array $paymentData)
    {
        return DB::table('payments')->insertGetId($paymentData);
    }

    public function findByOrderId($orderId)
    {
        return DB::table('payments')->where('order_id', $orderId)->first();
    }

    public function update($id, array $data)
    {
        return DB::table('payments')->where('id', $id)->update($data);
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

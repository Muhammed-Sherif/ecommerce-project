<?php 
namespace orders\application\commands;
use orders\application\commands\CreateOrderHandler;
use cart\domains\contracts\ICartApi;
use payments\shared\IPaymentApi;
use orders\domains\contracts\IOrderRepository;

class OrderCheckoutHandler { 

    private $paymentApi;
    private $orderRepository;

    public function __construct(CreateOrderHandler $createOrderHandler , ICartApi $cartApi , IPaymentApi $paymentApi, IOrderRepository $orderRepository) {
        $this->createOrderHandler = $createOrderHandler;
        $this->cartApi = $cartApi;
        $this->paymentApi = $paymentApi;
        $this->orderRepository = $orderRepository;
    }

    public function handle( $user) {
        $missingFields = $this->getMissingShippingFields($user);
        if (!empty($missingFields)) {
            return [
                'success' => false,
                'message' => 'Shipping address is required before checkout.',
                'missing_fields' => $missingFields
            ];
        }

        $cart = $this->cartApi->getCart($user->id);
        $result = $this->createOrderHandler->handle($cart->toArray() , $user);
        $this->cartApi->clearCart($user->id);
        \Log::info('Order: ' . json_encode($result));

        if ($result['success']) {
            $order = $result['order'];
            $paymentData = $this->paymentApi->getPaymentLink('egypt', (float)$order->total_amount , 'EGP', (string)$order->id);
            $paymentUrl = $paymentData['link'];

            $updateData = [
                'updated_at' => now(),
            ];

            if ($paymentUrl) {
                $updateData['payment_url'] = $paymentUrl;
            }
            
            if (!empty($paymentData['gateway_order_id'])) {
                $updateData['gateway_order_id'] = $paymentData['gateway_order_id'];
            }

            $this->orderRepository->update($order->id, $updateData);

            \Log::info('Payment URL: ' . $paymentUrl);
            return [
                'success' => true,
                'payment_url' => $paymentUrl
            ];
        }
        return [
            'success' => false,
            'message' => $result['message'] ?? 'Checkout failed.'
        ];
    }

    private function getMissingShippingFields($user): array
    {
        $requiredFields = [
            'shipping_street',
            'shipping_city',
            'shipping_state',
            'shipping_country',
            'shipping_zip_code'
        ];

        $missing = [];
        foreach ($requiredFields as $field) {
            $value = $user->$field ?? null;
            if (!is_string($value) || trim($value) === '') {
                $missing[] = $field;
            }
        }

        return $missing;
    }

}   

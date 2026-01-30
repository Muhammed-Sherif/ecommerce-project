<?php 
namespace orders\application\commands;
use orders\application\commands\CreateOrderHandler;
use cart\domains\contracts\ICartApi;
use payments\shared\IPaymentApi;
use orders\domains\contracts\IOrderRepository;
Use Coupons\application\queries\CheckValidityOfCouponByCodeHandler ; 
class OrderCheckoutHandler { 

    private $paymentApi;
    private $orderRepository;
    private $checkValidityOfCouponByCodeHandler;
    private $createOrderHandler;
    private $cartApi;

    public function __construct(CreateOrderHandler $createOrderHandler , ICartApi $cartApi , IPaymentApi $paymentApi, IOrderRepository $orderRepository, CheckValidityOfCouponByCodeHandler $checkValidityOfCouponByCodeHandler) {
        $this->createOrderHandler = $createOrderHandler;
        $this->cartApi = $cartApi;
        $this->paymentApi = $paymentApi;
        $this->orderRepository = $orderRepository;
        $this->checkValidityOfCouponByCodeHandler = $checkValidityOfCouponByCodeHandler;
    }

    public function handle( $user , $data) {
        $missingFields = $this->getMissingShippingFields($user);
        if (!empty($missingFields)) {
            return [
                'success' => false,
                'message' => 'Shipping address is required before checkout.',
                'missing_fields' => $missingFields
            ];
        }
        $cartDetails = $this->cartApi->getCart($user->id);
        $cart = $cartDetails['cart'];
        if (!$cart || $cart->count() === 0) {
            return [
                'success' => false,
                'message' => 'Cart is empty.'
            ];
        }
        $couponCodeInput = trim((string) ($data['coupon_code'] ?? ''));
        $couponCode = $couponCodeInput !== '' ? $couponCodeInput : null;
        $coupon = null;
        $discountedAmount = 0;

        if ($couponCode) {
            $validation = $this->checkValidityOfCouponByCodeHandler->handle($couponCode);
            if (!$validation['success']) {
                return [
                    'success' => false,
                    'message' => $validation['message'] ?? 'Invalid coupon code.'
                ];
            }
            $coupon = $validation['coupon'] ?? null;
            if (!$this->checkValidityOfCouponAmount($coupon, $cartDetails['total_amount'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid coupon amount for the order total.'
                ];
            }
            $discountedAmount = $this->calcTotalDiscountedAmount($cartDetails['total_amount'], $coupon);
        }

        $result = $this->createOrderHandler->handle(
            $cart->toArray(),
            $user,
            $coupon ? $coupon->code : null,
            $discountedAmount,
            $cartDetails['total_amount']
        );
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
    private function checkValidityOfCouponAmount($coupon , $orderTotal)
    {
        if (!$coupon) {
            return true;
        }
        if ($coupon->min_order_amount > 0 && $orderTotal < $coupon->min_order_amount) {
            return false;
        }
        if ($coupon->type === 'percentage' && $coupon->value > 100) {
            return false;
        }
        if ($coupon->type === 'fixed' && $coupon->value > $orderTotal) {
            return false;
        }
        return true;
    }
    private function calcTotalDiscountedAmount($orderTotal, $coupon)
    {
        if (!$coupon) {
            return 0;
        }
        if ($coupon->type === 'percentage') {
            $discount = ($coupon->value / 100) * $orderTotal;
        } else {
            $discount = $coupon->value;
        }
        return max(0, $discount);
    }
}   

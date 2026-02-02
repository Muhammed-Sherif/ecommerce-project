<?php 
namespace orders\application\commands;
use orders\application\commands\CreateOrderHandler;
use orders\domains\contracts\IOrderRepository;
use orders\domains\contracts\ICartGateway;
use orders\domains\contracts\IPaymentGateway;
use orders\domains\contracts\ICouponGateway;
use orders\domains\contracts\ICouponValidationGateway;
class OrderCheckoutHandler { 

    private $paymentApi;
    private $orderRepository;
    private $couponGateway;
    private $couponValidationGateway;
    private $createOrderHandler;
    private $cartGateway;

    public function __construct(CreateOrderHandler $createOrderHandler , ICartGateway $cartGateway , IPaymentGateway $paymentApi, IOrderRepository $orderRepository, ICouponGateway $couponGateway, ICouponValidationGateway $couponValidationGateway) {
        $this->createOrderHandler = $createOrderHandler;
        $this->cartGateway = $cartGateway;
        $this->paymentApi = $paymentApi;
        $this->orderRepository = $orderRepository;
        $this->couponGateway = $couponGateway;
        $this->couponValidationGateway = $couponValidationGateway;
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
        $cartDetails = $this->cartGateway->getCart($user->id);
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
            $validation = $this->couponValidationGateway->validateByCode($couponCode);
            if (!$validation['success']) {
                return [
                    'success' => false,
                    'message' => $validation['message'] ?? 'Invalid coupon code.'
                ];
            }
            
            $coupon = $validation['coupon'] ?? null;
            $applicableProducts = $validation['applicable_products'] ?? [];
            $applicableTotal = $validation['applicable_total'] ?? 0;
            $vendorName = $validation['vendor_name'] ?? '';
            
            // Check coupon amount against applicable total (not entire cart)
            if (!$this->checkValidityOfCouponAmount($coupon, $applicableTotal)) {
                return [
                    'success' => false,
                    'message' => 'Invalid coupon amount for the applicable products total.'
                ];
            }
            
            // Calculate discount based on applicable total
            $discountedAmount = $this->calcTotalDiscountedAmount($applicableTotal, $coupon);

            
        }

        $result = $this->createOrderHandler->handle(
            $cart->toArray(),
            $user,
            $coupon ? $coupon->code : null,
            $discountedAmount,
            $cartDetails['total_amount']
        );
        $this->cartGateway->clearCart($user->id);
        \Log::info('Order: ' . json_encode($result));

        if ($result['success']) {
            $order = $result['order'];
            $paymentData = $this->paymentApi->getPaymentLink('egypt', (float)$order->total_amount , 'EGP', (string)$order->id);
            \Log::info('Payment Data: ' . json_encode($paymentData));
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

            if ($coupon) {
                try {
                    $this->couponGateway->incrementUsedCount($coupon->id);
                } catch (\Throwable $e) {
                    \Log::warning('Failed to increment coupon usage', [
                        'coupon_id' => $coupon->id ?? null,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
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

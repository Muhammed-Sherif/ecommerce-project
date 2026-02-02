<?php
namespace Coupons\application\queries;

use Coupons\domains\contracts\IProductGateway;

class AdminCouponHandler
{
    private $productGateway;

    public function __construct(IProductGateway $productGateway)
    {
        $this->productGateway = $productGateway;
    }

    /**
     * Handle admin coupon validation - applies to ALL cart items
     * @param object $coupon
     * @param array $cartResult
     * @return array
     */
    public function handle($coupon, $cartResult)
    {
        // Admin coupons apply to ALL cart items without vendor validation
        $allProductIds = [];
        $totalAmount = 0;
        
        foreach ($cartResult['cart'] as $cartItem) {
            $product = $this->productGateway->findById($cartItem['product_id']);
            if ($product) {
                $allProductIds[] = $cartItem['product_id'];
                $totalAmount += $product->price * $cartItem['quantity'];
            }
        }
        
        return [
            'success' => true,
            'applicable_products' => $allProductIds,
            'applicable_total' => $totalAmount,
            'vendor_name' => ''
        ];
    }
}
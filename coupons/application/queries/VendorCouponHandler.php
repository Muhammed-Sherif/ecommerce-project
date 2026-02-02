<?php
namespace Coupons\application\queries;

use Coupons\domains\contracts\IProductGateway;
use Coupons\domains\contracts\IUserGateway;

class VendorCouponHandler
{
    private $productGateway;
    private $userGateway;

    public function __construct(
        IProductGateway $productGateway,
        IUserGateway $userGateway
    ) {
        $this->productGateway = $productGateway;
        $this->userGateway = $userGateway;
    }

    /**
     * Handle vendor coupon validation - applies only to vendor's products
     * @param object $coupon
     * @param array $cartResult
     * @return array
     */
    public function handle($coupon, $cartResult)
    {
        // Find cart items that belong to the same vendor who created the coupon
        $vendorProductIds = [];
        $vendorProductsTotal = 0;
        $vendorName = null;
        
        foreach ($cartResult['cart'] as $cartItem) {
            // Get product details to check vendor
            $product = $this->productGateway->findById($cartItem['product_id']);
            
            if ($product && $product->user_id == $coupon->user_id) {
                $vendorProductIds[] = $cartItem['product_id'];
                $vendorProductsTotal += $product->price * $cartItem['quantity'];
                
                // Get vendor name from product owner
                if (!$vendorName) {
                    $vendor = $this->userGateway->findById($product->user_id);
                    $vendorName = $vendor ? $vendor->name : 'Unknown Vendor';
                }
            }
        }

        // Check if there are any products from this vendor in the cart
        if (empty($vendorProductIds)) {
            return [
                'success' => false, 
                'message' => 'This coupon can only be applied to products from the coupon creator\'s store'
            ];
        }

        return [
            'success' => true,
            'applicable_products' => $vendorProductIds,
            'applicable_total' => $vendorProductsTotal,
            'vendor_name' => $vendorName
        ];
    }
}
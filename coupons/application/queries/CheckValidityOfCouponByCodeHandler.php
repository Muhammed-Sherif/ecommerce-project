<?php
namespace Coupons\application\queries;

use Coupons\domains\contracts\ICouponRepository;
use Coupons\application\queries\GetCouponByCode;
use Coupons\application\queries\AdminCouponHandler;
use Coupons\application\queries\VendorCouponHandler;
use Coupons\domains\contracts\IUserGateway;
use Coupons\domains\contracts\ICartGateway;

class CheckValidityOfCouponByCodeHandler
{
    private $repository;
    private $getCoupon;
    private $adminCouponHandler;
    private $vendorCouponHandler;
    private $userGateway;
    private $cartGateway;

    public function __construct(
        ICouponRepository $repository, 
        GetCouponByCode $getCoupon,
        AdminCouponHandler $adminCouponHandler,
        VendorCouponHandler $vendorCouponHandler,
        IUserGateway $userGateway,
        ICartGateway $cartGateway
    ) {
        $this->repository = $repository;
        $this->getCoupon = $getCoupon;
        $this->adminCouponHandler = $adminCouponHandler;
        $this->vendorCouponHandler = $vendorCouponHandler;
        $this->userGateway = $userGateway;
        $this->cartGateway = $cartGateway;
    }

    public function handle($code)
    {
        $result = $this->getCoupon->handle($code);
        if (!$result['success']) {
            return ['success' => false, 'message' => 'Coupon not found'];
        }
        
        $coupon = $result['coupon'];
        
        // Basic coupon validations
        if (!$coupon->is_active) {
            return ['success' => false, 'message' => 'Coupon is not active'];
        }
        
        if ($coupon->usage_limit > 0 && $coupon->used_count >= $coupon->usage_limit) {
            return ['success' => false, 'message' => 'Coupon redemption limit reached'];
        }
        
        if ($coupon->expires_at) {
            $expiresAt = $coupon->expires_at instanceof \DateTimeInterface
                ? $coupon->expires_at->getTimestamp()
                : strtotime((string) $coupon->expires_at);
            if ($expiresAt && $expiresAt < time()) {
                return ['success' => false, 'message' => 'Coupon has expired'];
            }
        }

        // Check if user is authenticated (required for cart access)
        $user = auth()->user();
        if (!$user) {
            return ['success' => false, 'message' => 'Authentication required to apply coupon'];
        }

        // Get cart once here
        $cartResult = $this->cartGateway->getCart($user->id);
        
        if (!$cartResult || !$cartResult['success'] || empty($cartResult['cart'])) {
            return [
                'success' => false, 
                'message' => 'Cart is empty'
            ];
        }

        // Determine which handler to use based on coupon creator's role
        $couponCreator = $this->userGateway->findById($coupon->user_id);
        if ($couponCreator && $couponCreator->role === 'admin' && $couponCreator->status === 'active') {
            // Use admin coupon handler
            $validation = $this->adminCouponHandler->handle($coupon, $cartResult);
        } else {
            // Use vendor coupon handler
            $validation = $this->vendorCouponHandler->handle($coupon, $cartResult);
        }
        
        if (!$validation['success']) {
            return $validation;
        }

        return [
            'success' => true, 
            'message' => 'Coupon is valid', 
            'coupon' => $coupon,
            'applicable_products' => $validation['applicable_products'],
            'applicable_total' => $validation['applicable_total'],
            'vendor_name' => $validation['vendor_name']
        ];
    }
}

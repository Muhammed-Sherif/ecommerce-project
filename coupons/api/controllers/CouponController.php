<?php
namespace Coupons\api\controllers;

use Coupons\application\commands\CreateCouponHandler;
use Coupons\application\commands\UpdateCouponHandler;
use Coupons\application\commands\DeleteCouponHandler;
use Coupons\application\queries\GetAllCouponsHandler;
use Coupons\application\queries\GetCouponHandler;
use Coupons\application\queries\GetCouponByCode;
use Coupons\application\queries\CheckValidityOfCouponByCodeHandler;

class CouponController
{
    public function index(GetAllCouponsHandler $handler)
    {
        return $handler->handle();
    }

    public function show($id, GetCouponHandler $handler)
    {
        return $handler->handle($id);
    }
    public function byCode($code, GetCouponByCode $handler)
    {
        return $handler->handle($code);
    }
    public function checkValidityByCode($code, CheckValidityOfCouponByCodeHandler $handler)
    {
        return $handler->handle($code);
    }
    public function store(array $data, CreateCouponHandler $handler)
    {
        try {
            return $handler->handle($data);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function update($id, array $data, UpdateCouponHandler $handler)
    {
        try {
            return $handler->handle($id, $data);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function destroy($id, DeleteCouponHandler $handler)
    {
        try {
            return $handler->handle($id);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

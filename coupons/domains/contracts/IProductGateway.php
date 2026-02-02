<?php
namespace Coupons\domains\contracts;

interface IProductGateway
{
    /**
     * Find product by ID
     * @param int $productId
     * @return object|null
     */
    public function findById($productId);
}
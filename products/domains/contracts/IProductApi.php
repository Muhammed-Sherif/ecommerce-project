<?php
namespace products\domains\contracts;

interface IProductApi
{
    /**
     * Find product by ID
     * @param int $productId
     * @return object|null
     */
    public function findById($productId);
}
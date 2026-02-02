<?php
namespace products\shared;

use products\application\queries\GetProductHandler;
use products\domains\contracts\IProductApi;

class ProductApi implements IProductApi
{
    private $getProductHandler;

    public function __construct(GetProductHandler $getProductHandler)
    {
        $this->getProductHandler = $getProductHandler;
    }

    /**
     * Find product by ID
     * @param int $productId
     * @return object|null
     */
    public function findById($productId)
    {
        $result = $this->getProductHandler->handle($productId);
        if ($result && isset($result["success"]) && $result["success"]) {
            return $result["product"] ?? null;
        }
        return null;
    }
}
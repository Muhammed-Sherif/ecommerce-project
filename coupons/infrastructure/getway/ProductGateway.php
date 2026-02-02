<?php
namespace Coupons\infrastructure\getway;

use Coupons\domains\contracts\IProductGateway;
use products\shared\ProductApi;

class ProductGateway implements IProductGateway
{
    private $productApi;

    public function __construct(ProductApi $productApi)
    {
        $this->productApi = $productApi;
    }

    /**
     * Find product by ID - Gateway only handles product communication
     * @param int $productId
     * @return object|null
     */
    public function findById($productId)
    {
        return $this->productApi->findById($productId);
    }
}
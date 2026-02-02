<?php
namespace products\application\queries;

use products\domains\contracts\IProductRepository;

class GetProductForDashboardHandler
{
    private $repository;
    private $getProduct;

    public function __construct(IProductRepository $repository, GetProduct $getProduct)
    {
        $this->repository = $repository;
        $this->getProduct = $getProduct;
    }

    public function handle($id)
    {
        $product = $this->repository->findByIdForDashboard($id);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }
        return ['success' => true, 'product' => $this->getProduct::execute($product)];
    }
}
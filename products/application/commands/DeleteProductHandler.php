<?php
namespace products\application\commands;

use products\domains\contracts\IProductRepository;

class DeleteProductHandler
{
    private $repository;
    private $deleteProduct;

    public function __construct(IProductRepository $repository, DeleteProduct $deleteProduct)
    {
        $this->repository = $repository;
        $this->deleteProduct = $deleteProduct;
    }

    public function handle($id)
    {
        $productId = $this->deleteProduct::execute($id);
        $existing = $this->repository->findById($productId);
        if (!$existing) {
            return ['success' => false, 'message' => 'Product not found'];
        }

        $this->repository->delete($productId);
        return ['success' => true, 'message' => 'Product deleted'];
    }
}

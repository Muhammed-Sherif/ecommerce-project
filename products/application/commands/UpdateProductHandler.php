<?php
namespace products\application\commands;

use products\domains\contracts\IProductRepository;

class UpdateProductHandler
{
    private $repository;
    private $updateProduct;

    public function __construct(IProductRepository $repository, UpdateProduct $updateProduct)
    {
        $this->repository = $repository;
        $this->updateProduct = $updateProduct;
    }

    public function handle($id, array $data)
    {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Product not found'];
        }

        $this->updateProduct::execute($data);
        $updated = $this->repository->update($id, $data);

        return ['success' => true, 'product' => $updated ?? $existing];
    }
}

<?php
namespace products\application\queries;

use products\domains\contracts\IProductRepository;

class GetAllProductsHandler
{
    private $repository;
    private $getAllProducts;

    public function __construct(IProductRepository $repository, GetAllProducts $getAllProducts)
    {
        $this->repository = $repository;
        $this->getAllProducts = $getAllProducts;
    }

    public function handle()
    {
        $products = $this->repository->getAll()->toArray();
        return ['success' => true, 'products' => $this->getAllProducts::execute($products ?? [])];
    }
}

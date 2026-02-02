<?php
namespace products\application\queries;

use products\domains\contracts\IProductRepository;

class GetAllProductsForDashboardHandler
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
        $products = $this->repository->getAllForDashboard()->toArray();
        return ['success' => true, 'products' => $products];
    }
}
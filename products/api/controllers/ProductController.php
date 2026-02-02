<?php
namespace products\api\controllers;

use products\application\commands\CreateProductHandler;
use products\application\commands\UpdateProductHandler;
use products\application\commands\DeleteProductHandler;
use products\application\queries\GetAllProductsHandler;
use products\application\queries\GetAllProductsForDashboardHandler;
use products\application\queries\GetAllActiveProductsPublicHandler;
use products\application\queries\GetProductHandler;
use products\application\queries\GetProductForDashboardHandler;
use products\application\queries\GetProductPublicHandler;

class ProductController
{
    public function index(GetAllProductsHandler $handler)
    {
        return $handler->handle();
    }

    public function show($id, GetProductHandler $handler)
    {
        return $handler->handle($id);
    }

    public function indexForDashboard(GetAllProductsForDashboardHandler $handler)
    {
        return $handler->handle();
    }

    public function indexActivePublic(GetAllActiveProductsPublicHandler $handler)
    {
        return $handler->handle();
    }

    public function showForDashboard($id, GetProductForDashboardHandler $handler)
    {
        return $handler->handle($id);
    }

    public function showPublic($id, GetProductPublicHandler $handler)
    {
        return $handler->handle($id);
    }

    public function store(array $data, CreateProductHandler $handler)
    {
        \Log::info('CreateProduct called with data: ' . json_encode($data));
        try {
            return $handler->handle($data);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function update($id, array $data, UpdateProductHandler $handler)
    {
        \Log::info('UpdateProduct called with data: ' . json_encode($data));
        try {
            return $handler->handle($id, $data);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function destroy($id, DeleteProductHandler $handler)
    {
        try {
            return $handler->handle($id);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

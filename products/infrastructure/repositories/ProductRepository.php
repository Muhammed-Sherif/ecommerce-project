<?php
namespace products\infrastructure\repositories;

use products\domains\contracts\IProductRepository;
use App\Models\Product;

class ProductRepository implements IProductRepository
{
    public function create(array $productData)
    {
        $product = Product::query()->create($productData);
        return $product->id;
    }

    public function update($id, array $productData)
    {
        $query = Product::query()->where('id', $id);
        return $query->update($productData);
    }

    public function delete($id)
    {
        $query = Product::query()->where('id', $id);
        return $query->delete();
    }

    public function findById($id)
    {
        $query = Product::query()->where('id', $id);
        return $query->first();
    }

    public function getAll()
    {
        $query = Product::query();
        return $query->orderBy('created_at', 'desc')->get();
    }
}

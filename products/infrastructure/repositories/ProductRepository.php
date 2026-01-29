<?php
namespace products\infrastructure\repositories;

use products\domains\contracts\IProductRepository;
use Illuminate\Support\Facades\DB;

class ProductRepository implements IProductRepository
{
    public function create(array $productData)
    {
        return DB::table('products')->insertGetId($productData);
    }

    public function update($id, array $productData)
    {
        return DB::table('products')
            ->where('id', $id)
            ->update($productData);
    }

    public function delete($id)
    {
        return DB::table('products')->where('id', $id)->delete();
    }

    public function findById($id)
    {
        return DB::table('products')->where('id', $id)->first();
    }

    public function getAll()
    {
        return DB::table('products')->orderBy('created_at', 'desc')->get();
    }
}

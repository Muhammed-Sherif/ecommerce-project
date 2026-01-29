<?php
namespace products\domains\contracts;

interface IProductRepository
{
    public function create(array $productData);
    public function update($id, array $productData);
    public function delete($id);
    public function findById($id);
    public function getAll();
}

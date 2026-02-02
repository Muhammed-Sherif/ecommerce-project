<?php
namespace Coupons\domains\contracts;

interface ICouponRepository
{
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function findById($id);
    public function getAll();
    public function findByCode($code);
    public function incrementUsedCount($id);
}

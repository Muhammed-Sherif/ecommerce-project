<?php
namespace Coupons\infrastructure\repositories;

use Coupons\domains\contracts\ICouponRepository;
use App\Models\Coupon;

class CouponRepository implements ICouponRepository
{
    public function create(array $data)
    {
        $coupon = Coupon::query()->create($data);
        return $coupon->id;
    }

    public function update($id, array $data)
    {
        $query = Coupon::query()->where('id', $id);
        return $query->update($data);
    }

    public function delete($id)
    {
        $query = Coupon::query()->where('id', $id);
        return $query->delete();
    }

    public function findById($id)
    {
        $query = Coupon::query()->where('id', $id);
        return $query->first();
    }

    public function getAll()
    {
        $query = Coupon::query();
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function findByCode($code)
    {
        $query = Coupon::query()->where('code', $code);
        return $query->first();
    }
    public function redemptionCount($id)
    {
        return Coupon::where('id', $id)->couponRedemptions()->count();
    }
}

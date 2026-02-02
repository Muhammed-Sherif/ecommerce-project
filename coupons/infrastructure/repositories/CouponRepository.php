<?php
namespace Coupons\infrastructure\repositories;

use Coupons\domains\contracts\ICouponRepository;
use App\Models\Coupon;

class CouponRepository implements ICouponRepository
{
    public function create(array $data)
    {
        $user = auth()->user();
        if (!$user) {
            throw new \Exception('Authentication required for creating coupons');
        }
        $data['user_id'] = $user->id;
        $coupon = Coupon::query()->create($data);
        return $coupon->id;
    }

    public function update($id, array $data)
    {
        $user = auth()->user();
        if (!$user) {
            throw new \Exception('Authentication required for updating coupons');
        }
        
        if ($user->role === 'admin' && $user->status === 'active') {
            return Coupon::query()->where('id', $id)->update($data);
        }
        return Coupon::query()->where('id', $id)->where('user_id', $user->id)->update($data);
    }

    public function delete($id)
    {
        $user = auth()->user();
        if (!$user) {
            throw new \Exception('Authentication required for deleting coupons');
        }
        
        if ($user->role === 'admin' && $user->status === 'active') {
            return Coupon::query()->where('id', $id)->delete();
        }
        return Coupon::query()->where('id', $id)->where('user_id', $user->id)->delete();
    }

    public function findById($id)
    {
        $user = auth()->user();
        if (!$user) {
            throw new \Exception('Authentication required for accessing coupons');
        }
        
        if ($user->role === 'admin' && $user->status === 'active') {
            return Coupon::query()->where('id', $id)->first();
        }
        return Coupon::query()->where('id', $id)->where('user_id', $user->id)->first();
    }

    public function getAll()
    {
        $user = auth()->user();
        if (!$user) {
            throw new \Exception('Authentication required for accessing coupons');
        }
        
        if ($user->role === 'admin' && $user->status === 'active' ) {
            return Coupon::query()->orderBy('created_at', 'desc')->get();
        }
        return Coupon::query()->where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
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

    public function incrementUsedCount($id)
    {
        return Coupon::query()->where('id', $id)->increment('used_count');
    }
}

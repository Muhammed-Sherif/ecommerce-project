<?php
namespace reviews\infrastructure\repositories;

use reviews\domains\contracts\IReviewRepository;
use Illuminate\Support\Facades\DB;

class ReviewRepository implements IReviewRepository
{
    public function create(array $reviewData)
    {
        return DB::table('reviews')->insertGetId($reviewData);
    }

    public function update($id, array $reviewData)
    {
        return DB::table('reviews')
            ->where('id', $id)
            ->update($reviewData);
    }

    public function delete($id)
    {
        return DB::table('reviews')->where('id', $id)->delete();
    }

    public function findById($id)
    {
        return DB::table('reviews')->where('id', $id)->first();
    }

    public function findByProduct($productId, array $filters = [])
    {
        $query = DB::table('reviews')->where('product_id', $productId);

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply pagination
        if (!empty($filters['per_page'])) {
            $perPage = $filters['per_page'];
            $page = $filters['page'] ?? 1;
            $offset = ($page - 1) * $perPage;

            $query->limit($perPage)->offset($offset);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function findByUser($userId)
    {
        return DB::table('reviews')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAll(array $filters = [])
    {
        $query = DB::table('reviews');

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply pagination
        if (!empty($filters['per_page'])) {
            $perPage = $filters['per_page'];
            $page = $filters['page'] ?? 1;
            $offset = ($page - 1) * $perPage;

            $query->limit($perPage)->offset($offset);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getAverageRating($productId)
    {
        $result = DB::table('reviews')
            ->where('product_id', $productId)
            ->where('status', 'approved')
            ->avg('rating');

        return $result ? round($result, 1) : 0;
    }
}

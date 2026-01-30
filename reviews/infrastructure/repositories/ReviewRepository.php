<?php
namespace reviews\infrastructure\repositories;

use reviews\domains\contracts\IReviewRepository;
use App\Models\Review;

class ReviewRepository implements IReviewRepository
{
    public function create(array $reviewData)
    {
        $review = Review::query()->create($reviewData);
        return $review;
    }

    public function update($id, array $reviewData)
    {
        $query = Review::query()->where('id', $id);
        return $query->update($reviewData);
    }

    public function delete($id)
    {
        $query = Review::query()->where('id', $id);
        return $query->delete();
    }

    public function findById($id)
    {
        $query = Review::query()->where('id', $id);
        return $query->first();
    }

    public function findByProduct($productId, array $filters = [])
    {
        $query = Review::query()->where('product_id', $productId);

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
        $query = Review::query()->where('user_id', $userId);
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getAll(array $filters = [])
    {
        $query = Review::query();

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
        $query = Review::query()
            ->where('product_id', $productId)
            ->where('status', 'approved');
        $result = $query->avg('rating');

        return $result ? round($result, 1) : 0;
    }
}

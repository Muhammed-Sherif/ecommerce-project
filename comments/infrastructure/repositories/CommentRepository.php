<?php
namespace comments\infrastructure\repositories;

use comments\domains\contracts\ICommentRepository;
use App\Models\Comment;

class CommentRepository implements ICommentRepository
{
    public function create(array $commentData)
    {
        $comment = Comment::query()->create($commentData);
        return $comment->id;
    }

    public function update($id, array $commentData)
    {
        $query = Comment::query()->where('id', $id);
        return $query->update($commentData);
    }

    public function delete($id)
    {
        $query = Comment::query()->where('id', $id);
        return $query->delete();
    }

    public function findById($id)
    {
        $query = Comment::query()->where('id', $id);
        return $query->first();
    }

    public function getAll()
    {
        $query = Comment::query();
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getByProduct($productId)
    {
        $query = Comment::query()->where('product_id', $productId);
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getByUser($userId)
    {
        $query = Comment::query()->where('user_id', $userId);
        return $query->orderBy('created_at', 'desc')->get();
    }
}

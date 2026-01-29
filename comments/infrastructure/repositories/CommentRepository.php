<?php
namespace comments\infrastructure\repositories;

use comments\domains\contracts\ICommentRepository;
use Illuminate\Support\Facades\DB;

class CommentRepository implements ICommentRepository
{
    public function create(array $commentData)
    {
        return DB::table('comments')->insertGetId($commentData);
    }

    public function update($id, array $commentData)
    {
        return DB::table('comments')
            ->where('id', $id)
            ->update($commentData);
    }

    public function delete($id)
    {
        return DB::table('comments')->where('id', $id)->delete();
    }

    public function findById($id)
    {
        return DB::table('comments')->where('id', $id)->first();
    }

    public function getAll()
    {
        return DB::table('comments')->orderBy('created_at', 'desc')->get();
    }

    public function getByProduct($productId)
    {
        return DB::table('comments')->where('product_id', $productId)->orderBy('created_at', 'desc')->get();
    }

    public function getByUser($userId)
    {
        return DB::table('comments')->where('user_id', $userId)->orderBy('created_at', 'desc')->get();
    }
}

<?php
namespace reviews\api\controllers;

use reviews\application\commands\CreateReviewHandler;
use reviews\application\commands\ApproveReviewHandler;
use reviews\application\commands\DeleteReviewHandler;
use reviews\application\queries\GetProductReviewsHandler;

class ReviewController
{
    public function byProduct($productId, GetProductReviewsHandler $handler)
    {
        return $handler->handle(['product_id' => $productId]);
    }

    public function store(array $data, CreateReviewHandler $handler)
    {
        try {
            return $handler->handle($data);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function approve($id, ApproveReviewHandler $handler)
    {
        try {
            return $handler->handle(['review_id' => $id]);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function destroy($id, DeleteReviewHandler $handler)
    {
        try {
            return $handler->handle(['review_id' => $id]);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

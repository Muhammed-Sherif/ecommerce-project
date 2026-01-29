<?php
namespace reviews\infrastructure\controllers;

use reviews\application\commands\CreateReviewHandler;
use reviews\application\commands\ApproveReviewHandler;
use reviews\application\commands\DeleteReviewHandler;
use reviews\application\queries\GetProductReviewsHandler;

class ReviewController
{
    private $createReviewHandler;
    private $approveReviewHandler;
    private $deleteReviewHandler;
    private $getProductReviewsHandler;

    public function __construct(
        CreateReviewHandler $createReviewHandler,
        ApproveReviewHandler $approveReviewHandler,
        DeleteReviewHandler $deleteReviewHandler,
        GetProductReviewsHandler $getProductReviewsHandler
    ) {
        $this->createReviewHandler = $createReviewHandler;
        $this->approveReviewHandler = $approveReviewHandler;
        $this->deleteReviewHandler = $deleteReviewHandler;
        $this->getProductReviewsHandler = $getProductReviewsHandler;
    }

    /**
     * Create a new review
     * POST /reviews
     */
    public function create($request)
    {
        try {
            $data = $request->all();
            $result = $this->createReviewHandler->handle($data);

            return response()->json($result, 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to create review: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get reviews for a product
     * GET /products/{productId}/reviews
     */
    public function getProductReviews($request, $productId)
    {
        try {
            $data = $request->all();
            $data['product_id'] = $productId;
            
            $result = $this->getProductReviewsHandler->handle($data);

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch reviews: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approve a review (Admin only)
     * PUT /reviews/{id}/approve
     */
    public function approve($request, $id)
    {
        try {
            $result = $this->approveReviewHandler->handle(['review_id' => $id]);

            return response()->json($result, 200);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to approve review: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a review
     * DELETE /reviews/{id}
     */
    public function delete($request, $id)
    {
        try {
            $result = $this->deleteReviewHandler->handle(['review_id' => $id]);

            return response()->json($result, 200);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete review: ' . $e->getMessage(),
            ], 500);
        }
    }
}

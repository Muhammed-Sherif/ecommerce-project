<?php
namespace reviews\application\queries;

use reviews\domains\contracts\IReviewRepository;

class GetProductReviewsHandler
{
    private $repository;
    private $getProductReviews;

    public function __construct(IReviewRepository $repository, GetProductReviews $getProductReviews)
    {
        $this->repository = $repository;
        $this->getProductReviews = $getProductReviews;
    }

    public function handle(array $data)
    {
        // Validate and prepare filters
        $filters = $this->getProductReviews::execute($data);

        // Fetch reviews
        $reviews = $this->repository->findByProduct($filters['product_id'], $filters);

        // Get average rating
        $averageRating = $this->repository->getAverageRating($filters['product_id']);

        return [
            'success' => true,
            'reviews' => $reviews,
            'average_rating' => $averageRating,
            'total_reviews' => count($reviews),
        ];
    }
}

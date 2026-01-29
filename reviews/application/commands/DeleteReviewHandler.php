<?php
namespace reviews\application\commands;

use reviews\domains\contracts\IReviewRepository;

class DeleteReviewHandler
{
    private $repository;
    private $deleteReview;

    public function __construct(IReviewRepository $repository, DeleteReview $deleteReview)
    {
        $this->repository = $repository;
        $this->deleteReview = $deleteReview;
    }

    public function handle(array $data)
    {
        // Validate data
        $validatedData = $this->deleteReview::execute($data);

        // Check if review exists
        $review = $this->repository->findById($validatedData['review_id']);

        if (!$review) {
            throw new \RuntimeException('Review not found');
        }

        // Delete review
        $this->repository->delete($validatedData['review_id']);

        return [
            'success' => true,
            'message' => 'Review deleted successfully',
        ];
    }
}

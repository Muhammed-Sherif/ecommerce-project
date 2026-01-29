<?php
namespace reviews\application\commands;

use reviews\domains\contracts\IReviewRepository;
use reviews\domains\models\ReviewStatus;

class ApproveReviewHandler
{
    private $repository;
    private $approveReview;

    public function __construct(IReviewRepository $repository, ApproveReview $approveReview)
    {
        $this->repository = $repository;
        $this->approveReview = $approveReview;
    }

    public function handle(array $data)
    {
        // Validate data
        $validatedData = $this->approveReview::execute($data);

        // Get review
        $review = $this->repository->findById($validatedData['review_id']);

        if (!$review) {
            throw new \RuntimeException('Review not found');
        }

        // Update status to approved
        $this->repository->update($validatedData['review_id'], [
            'status' => ReviewStatus::APPROVED,
        ]);

        // Fetch updated review
        $updatedReview = $this->repository->findById($validatedData['review_id']);

        return [
            'success' => true,
            'review' => $updatedReview,
        ];
    }
}

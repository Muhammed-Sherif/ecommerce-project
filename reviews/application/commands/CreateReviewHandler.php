<?php
namespace reviews\application\commands;

use reviews\domains\contracts\IReviewRepository;
use reviews\domains\models\ReviewStatus;

class CreateReviewHandler
{
    private $repository;
    private $createReview;

    public function __construct(IReviewRepository $repository, CreateReview $createReview)
    {
        $this->repository = $repository;
        $this->createReview = $createReview;
    }

    public function handle(array $data)
    {
        // Validate data
        $validatedData = $this->createReview::execute($data);

        // Add default status
        $validatedData['status'] = ReviewStatus::PENDING;

        // Create review
        $reviewId = $this->repository->create($validatedData);

        // Fetch created review
        $createdReview = $this->repository->findById($reviewId);

        return [
            'success' => true,
            'review' => $createdReview,
        ];
    }
}

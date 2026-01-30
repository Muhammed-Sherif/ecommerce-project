<?php
namespace reviews\application\commands;

use reviews\domains\contracts\IReviewRepository;
use reviews\domains\models\ReviewStatus;
use App\Events\ReviewCreated;

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
        $validatedData['status'] = ReviewStatus::APPROVED;

        // Create review
        $createdReview = $this->repository->create($validatedData);

        if ($createdReview) {
            ReviewCreated::dispatch($createdReview);
        }

        return [
            'success' => true,
            'review' => $createdReview,
        ];
    }
}

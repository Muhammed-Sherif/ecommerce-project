<?php
namespace reviews\application\commands;

use reviews\domains\contracts\IReviewRepository;
use reviews\domains\models\ReviewStatus;
use App\Events\ReviewCreated;
use reviews\domains\contracts\IOrdersQueriesGetway;

class CreateReviewHandler
{
    private $repository;
    private $createReview;
    private $ordersQueries;

    public function __construct(
        IReviewRepository $repository,
        CreateReview $createReview,
        IOrdersQueriesGetway $ordersQueries
    )
    {
        $this->repository = $repository;
        $this->createReview = $createReview;
        $this->ordersQueries = $ordersQueries;
    }

    public function handle(array $data)
    {
        // Validate data
        $validatedData = $this->createReview::execute($data);

        $eligibility = $this->ordersQueries->hasDeliveredProductForCustomer(
            $validatedData['user_id'],
            $validatedData['product_id']
        );

        if (!$eligibility || empty($eligibility->canReview)) {
            throw new \RuntimeException('You should buy it first.');
        }

        // Add default status
        $validatedData['status'] = ReviewStatus::APPROVED;

        // Create review
        $createdReview = $this->repository->create($validatedData);

        if ($createdReview) {
            broadcast(new ReviewCreated($createdReview));
        }

        return [
            'success' => true,
            'review' => $createdReview,
        ];
    }
}

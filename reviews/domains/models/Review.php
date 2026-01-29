<?php
namespace reviews\domains\models;

class Review
{
    public $id;
    public $productId;
    public $userId;
    public $userName;
    public $rating;
    public $title;
    public $comment;
    public $status;
    public $createdAt;
    public $updatedAt;

    public function __construct(
        $id,
        $productId,
        $userId,
        string $userName,
        int $rating,
        string $title,
        string $comment,
        string $status = ReviewStatus::PENDING,
        $createdAt = null,
        $updatedAt = null
    ) {
        $this->id = $id;
        $this->productId = $productId;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->rating = $rating;
        $this->title = $title;
        $this->comment = $comment;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['product_id'] ?? null,
            $data['user_id'] ?? null,
            $data['user_name'] ?? '',
            (int) ($data['rating'] ?? 0),
            $data['title'] ?? '',
            $data['comment'] ?? '',
            $data['status'] ?? ReviewStatus::PENDING,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null
        );
    }

    public function canBeEdited(): bool
    {
        return $this->status === ReviewStatus::PENDING || 
               $this->status === ReviewStatus::APPROVED;
    }

    public function canBeDeleted(): bool
    {
        return true; // Reviews can always be deleted by admin or owner
    }

    public function isApproved(): bool
    {
        return $this->status === ReviewStatus::APPROVED;
    }

    public function isValidRating(): bool
    {
        return $this->rating >= 1 && $this->rating <= 5;
    }
}

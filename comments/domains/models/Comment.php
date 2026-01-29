<?php
namespace comments\domains\models;

class Comment
{
    public $id;
    public $productId;
    public $userId;
    public $content;
    public $rating;
    public $status;

    public function __construct(
        $id,
        $productId,
        $userId,
        string $content,
        ?int $rating,
        string $status = 'active'
    ) {
        $this->id = $id;
        $this->productId = $productId;
        $this->userId = $userId;
        $this->content = $content;
        $this->rating = $rating;
        $this->status = $status;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['product_id'] ?? null,
            $data['user_id'] ?? null,
            $data['content'] ?? '',
            isset($data['rating']) ? (int) $data['rating'] : null,
            $data['status'] ?? 'active'
        );
    }
}

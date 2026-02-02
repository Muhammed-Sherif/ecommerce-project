<?php

namespace App\Events;

use App\Models\Review;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ReviewCreated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public Review $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('reviews.product.' . $this->review->product_id);
    }

    public function broadcastAs(): string
    {
        return 'review.created';
    }

    public function broadcastWith(): array
    {
        return [
            'review' => [
                'id' => $this->review->id,
                'product_id' => $this->review->product_id,
                'user_id' => $this->review->user_id,
                'user_name' => $this->review->user_name,
                'rating' => $this->review->rating,
                'title' => $this->review->title,
                'comment' => $this->review->comment,
                'status' => $this->review->status,
                'created_at' => $this->review->created_at,
            ],
        ];
    }
}

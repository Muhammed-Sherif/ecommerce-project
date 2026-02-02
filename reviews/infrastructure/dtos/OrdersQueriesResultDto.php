<?php
namespace reviews\infrastructure\dtos;

class OrdersQueriesResultDto
{
    public $canReview;

    public function __construct($result = null)
    {
        $this->canReview = (bool) $result;
    }

    public static function fromResult($result): self
    {
        return new self($result);
    }
}

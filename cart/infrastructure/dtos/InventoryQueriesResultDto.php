<?php
namespace cart\infrastructure\dtos;

class InventoryQueriesResultDto
{
    public $result;
    public $success;
    public $quantity;
    public $reserved_quantity;
    public $product_id;

    public function __construct($result = null)
    {
        $this->result = $result;
        $this->success = (bool) $result;
        if ($result) {
            $this->quantity = $result->quantity ?? null;
            $this->reserved_quantity = $result->reserved_quantity ?? null;
            $this->product_id = $result->product_id ?? null;
        }
    }

    public static function fromResult($result): self
    {
        return new self($result);
    }
}

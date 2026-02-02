<?php
namespace products\infrastructure\dtos;

class InventoryCommandResultDto
{
    public $result;
    public $success;
    public $quantity;
    public $product_id;

    public function __construct( $result = null)
    {       
        $this->result = $result;
        $this->success = (bool) $result;
        if ($result) {
            $this->quantity = $result->quantity ?? null;
            $this->product_id = $result->product_id ?? null;
        }
    }

    public static function fromResult($result): self
    {
        return new self($result);
    }
}

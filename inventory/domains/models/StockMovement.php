<?php
namespace inventory\domains\models;

class StockMovement
{
    public $id;
    public $inventoryId;
    public $type;
    public $quantity;
    public $reason;
    public $createdBy;
    public $createdAt;

    public function __construct(
        $id,
        $inventoryId,
        string $type,
        int $quantity,
        string $reason,
        $createdBy = null,
        $createdAt = null
    ) {
        $this->id = $id;
        $this->inventoryId = $inventoryId;
        $this->type = $type;
        $this->quantity = $quantity;
        $this->reason = $reason;
        $this->createdBy = $createdBy;
        $this->createdAt = $createdAt;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['inventory_id'] ?? null,
            $data['type'] ?? StockMovementType::ADJUSTMENT,
            (int) ($data['quantity'] ?? 0),
            $data['reason'] ?? '',
            $data['created_by'] ?? null,
            $data['created_at'] ?? null
        );
    }
}

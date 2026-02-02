<?php
namespace products\domains\models;

class Product
{
    public $id;
    public $name;
    public $description;
    public $price;
    public $quantity;
    public $category;
    public $status;
    public $events = [];
    public function __construct(
        $id,
        string $name,
        string $description,
        float $price,
        int $quantity,
        string $category,
        string $status = 'active'
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->category = $category;
        $this->status = $status;
    }
    public function addEvent($event): void
    {
        $this->events[] = $event;
    }
    public function getEvents(): array
    {
        return $this->events;
    }
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['name'] ?? '',
            $data['description'] ?? '',
            (float) ($data['price'] ?? 0),
            (int) ($data['quantity'] ?? 0),
            $data['category'] ?? 'general',
            $data['status'] ?? 'active'
        );
    }
}

<?php
namespace orders\domains\models;
use orders\domains\models\OrderCreatedEvent;
class Order
{
    public $id;
    public $orderNumber;
    public $customerId;
    public $status;
    public $totalAmount;
    public $shippingStreet;
    public $shippingCity;
    public $shippingState;
    public $shippingCountry;
    public $shippingZipCode;
    public $phone;
    public $createdAt;
    public $updatedAt;
    public $items = [];
    public $events = [];

    public function __construct(
        $id,
        string $orderNumber,
        $customerId,
        array $items,
        string $shippingStreet,
        string $shippingCity,
        string $shippingState,
        string $shippingCountry,
        string $shippingZipCode,
        string $phone = null,
        $createdAt = null,
        $updatedAt = null
    ) {
        $this->id = $id ?? null;
        $this->orderNumber = $orderNumber;
        $this->customerId = $customerId ?? throw new \InvalidArgumentException('Customer ID is required');
        $this->items = $items;
        $this->status = OrderStatus::PENDING;
        $this->totalAmount = $this->calculateTotalAmount();
        $this->shippingStreet = $shippingStreet;
        $this->shippingCity = $shippingCity;
        $this->shippingState = $shippingState;
        $this->shippingCountry = $shippingCountry;
        $this->shippingZipCode = $shippingZipCode;
        $this->phone = $phone;
        $this->createdAt = $createdAt ?? null;
        $this->updatedAt = $updatedAt ?? null;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['order_number'],
            $data['customer_id'],
            $data['items'] ?? [],
            $data['shipping_street'] ?? '',
            $data['shipping_city'] ?? '',
            $data['shipping_state'] ?? '',
            $data['shipping_country'] ?? '',
            $data['shipping_zip_code'] ?? '',
            $data['phone'] ?? null,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null
        );
    }
    private function calculateTotalAmount(): string
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->totalPrice;
        }
        return $total;
    }
    public function addEvent(OrderCreatedEvent $event): void
    {
        $this->events[] = $event;
    }
    
    private function getEvents(): array
    {
        return $this->events;
    }
    
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            OrderStatus::PENDING,
            OrderStatus::PAID
        ]);
    }

    public function canBeShipped(): bool
    {
        return $this->status === OrderStatus::PAID;
    }

    public function calculateTotal(): float
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->totalPrice;
        }
        return $total;
    }

    public function addItem(OrderItem $item): void
    {
        $this->items[] = $item;
    }
}

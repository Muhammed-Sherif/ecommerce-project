<?php
namespace notifications\internal\infrastructure;

use shared\IEventListener;
use shared\IEventBus;
use notifications\internal\domain\events\ProductDataReady;
use App\Models\Order;
use notifications\internal\domain\events\OrderNeedProductDataForConfirmationDto;
class OrderNeedProductDataForConfirmationHandler  
{
    public function __construct(IEventBus $eventBus)
    {
        $this->eventBus = $eventBus;
        $this->subscribe($this);
    }
    public function subscribe(IEventListener $listener): void
    {
        $this->eventBus->subscribe($this, OrderNeedProductDataForConfirmationDto::class, $this::handle);
    }

    public function unsubscribe(IEventListener $listener): void
    {
        $this->eventBus->unsubscribe($this);
    }

    public function handle(object $event): void
    {
        Order::query()->where('id', $event->orderId)->update([
            'status' => 'confirmed',
        ]);
        $this->eventBus->push(new ProductDataReady($event->orderId, $event->eventType, $event->occurredOn, $event->data));
    }
}

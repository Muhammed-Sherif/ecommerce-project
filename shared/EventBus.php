<?php
namespace shared;

final class EventBus implements IEventBus
{
    private array $listeners = [];

    public function subscribe(object $owner, string $eventClass, callable $callback): void
    {
        $this->listeners[] = new EventListener($owner, $eventClass, $callback);
    }

    public function unsubscribe(object $owner): void
    {
        $this->listeners = array_values(array_filter(
            $this->listeners,
            fn (EventListener $l) => $l->owner !== $owner
        ));
    }

    public function push(object $event): void
    {
        foreach ($this->listeners as $listener) {
            if ($listener->matches($event)) {
                ($listener->callback)($event);
            }
        }
    }
}
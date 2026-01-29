<?php
namespace shared;
final class EventListener
{
    public function __construct(
        public object $owner,
        public string $eventClass,
        public  $callback,
    ) {}

    public function matches(object $event): bool
    {
        return is_a($event, $this->eventClass);
    }
}
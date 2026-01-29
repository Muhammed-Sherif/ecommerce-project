<?php
namespace shared;
interface IEventBus
{
    /**
     * @param object   $owner      The subscriber owner (used for unsubscribe).
     * @param string   $eventClass Fully-qualified class name of the event.
     * @param callable $callback   function(object $event): void
     */
    public function subscribe(object $owner, string $eventClass, callable $callback): void;

    public function unsubscribe(object $owner): void;

    /**
     * Push an event instance to all matching listeners.
     */
    public function push(object $event): void;
}

<?php
namespace orders\infrastructure;
use shared\EventBus;
class OrderPublisher
{
    public function push(object $event): void
    {

        EventBus::push($event);
    }
}
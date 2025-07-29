<?php
namespace App\Core;

include_once './src/core/events/EventsInterface.php';

class Events implements EventsInterface
{
    private array $data = [];

    public function get(string $id)
    {
        return $this->data[$id] ?? null;
    }

    public function getAll(): array
    {
        return $this->data;
    }

    public function addItem(string $id, $event): void
    {
        $this->data[$id] = $event;
    }

    public function reset():void
    {
        $this->data = [];
    }
}

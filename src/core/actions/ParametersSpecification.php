<?php
namespace App\Core\Actions;

class ParametersSpecification
{
    public function __construct(private array $data = [])
    {}

    public function get(string $id)
    {
        return $this->eventsData[$id] ?? null;
    }

    public function getAll(): array
    {
        return $this->data;
    }

    public function addItem(string $id, $parameter): void
    {
        $this->data[$id] = $parameter;
    }

    public function reset():void
    {
        $this->data = [];
    }
}

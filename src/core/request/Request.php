<?php
namespace App\Core;

include_once './src/core/request/RequestInterface.php';

class Request implements RequestInterface
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

    public function addItem(string $id, $value): void
    {
        $this->data[$id] = $value;
    }

    public function reset():void
    {
        $this->data = [];
    }
}

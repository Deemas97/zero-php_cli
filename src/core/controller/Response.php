<?php
namespace App\Core\Controller;

include_once './src/core/controller/ControllerResponseInterface.php';

class Response implements ControllerResponseInterface
{
    protected array $data = [];

    public function get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    public function getAll(): array
    {
        return $this->data;
    }

    public function addItem(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function reset():void
    {
        $this->data = [];
    }
}
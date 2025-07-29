<?php
namespace App\Interfaces;

include_once './src/core/response/ResponseInterface.php';

use App\Core\ResponseInterface;

class Response implements ResponseInterface
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

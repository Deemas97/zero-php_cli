<?php
namespace App\Interfaces\CommandLine;

include_once './src/interfaces/command_line/CommandLineResponseInterface.php';

class Response implements CommandLineResponseInterface
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
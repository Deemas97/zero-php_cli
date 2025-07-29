<?php
namespace App\Interfaces\CommandLine;

include_once './src/interfaces/command_line/PrimitivesInterface.php';

class Primitives implements PrimitivesInterface
{
    private array $primitives = [];

    public function add(array $primitives): void
    {
        foreach ($primitives as $primitive) {
            $this->primitives[] = $primitive;
        }
    }

    public function get(int $index): ?Primitive
    {
        return $this->primitives[$index] ?? null;
    }

    public function getAll(): array
    {
        return $this->primitives;
    }
}
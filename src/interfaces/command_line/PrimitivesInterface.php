<?php
namespace App\Interfaces\CommandLine;

interface PrimitivesInterface
{
    public function add(array $primitives): void;
    public function get(int $index): ?Primitive;
}
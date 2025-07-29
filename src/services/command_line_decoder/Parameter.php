<?php
namespace App\Services\CommandLineDecoder;

class Parameter
{
    public function __construct(
        private readonly int|string $name,
        private readonly string $type,
        private $value,
    )
    {}

    public function getName(): int|string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
    }
}
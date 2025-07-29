<?php
namespace App\Services\CommandLineDecoder;

include_once './src/services/command_line_decoder/Parameter.php';

class ParametersDecoded
{
    const TYPES_MAP = [
        0 => 'command',
        1 => 'option',
        2 => 'flag',
        3 => 'argument'
    ];

    private array $parameters = [];
    private int $count = 0;
    private int $argumentsIndex = 0;

    public function addCommand(string $name): void
    {
        $this->add($name, self::TYPES_MAP[0]);
    }

    public function addOption(string $name, $value = true): void
    {
        $this->add($name, self::TYPES_MAP[1], $value);
    }

    public function addFlag(string $name): void
    {
        $this->add($name, self::TYPES_MAP[2], true);
    }

    public function addArgument($value): void
    {
        $this->add($this->argumentsIndex++, self::TYPES_MAP[3], $value);
    }

    public function get(int $id): ?Parameter
    {
        return $this->parameters[$id] ?? null;
    }

    public function getAll(): array
    {
        return $this->parameters;
    }

    protected function add(int|string $name, string $type, $value = null): void
    {
        $this->parameters[] = new Parameter($name, $type, $value);
        $this->count++;
    }
}

<?php
namespace App\Interfaces\CommandLine;

include_once './src/interfaces/command_line/primitives/PrimitiveResponseInterface.php';
include_once './src/services/command_line_decoder/Parameter.php';

use App\Interfaces\CommandLine\Primitives\PrimitiveResponseInterface;
use App\Services\CommandLineDecoder\Parameter;

abstract class Primitive
{
    public function __construct(
        protected string $name,
        protected bool $isRequired,
        protected ?string $valueType
    )
    {}

    public function getName(): string
    {
        return $this->name;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function getValueType(): ?string
    {
        return $this->valueType;
    }

    abstract public function validate(Parameter $parameter): PrimitiveResponseInterface;
}
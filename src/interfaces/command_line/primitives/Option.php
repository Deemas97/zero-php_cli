<?php
namespace App\Interfaces\CommandLine\Primitives;

include_once './src/interfaces/command_line/Primitive.php';
include_once './src/interfaces/command_line/primitives/Response.php';
include_once './src/interfaces/command_line/primitives/PrimitiveResponseInterface.php';
include_once './src/services/command_line_decoder/Parameter.php';

use App\Interfaces\CommandLine\Primitive;
use App\Services\CommandLineDecoder\Parameter;
use DateTime;

class Option extends Primitive
{
    const VALUE_TYPES_MAP = [
        0 => 'integer',
        1 => 'float',
        2 => 'boolean',
        3 => 'string',
        4 => 'date'
    ];

    const DATE_FORMAT = 'Y-m-d';

    public function __construct(
        string $name,
        bool $isRequired = false,
        ?string $valueType = null,
        protected ?array $valuesAcceptable = null
    )
    {
        parent::__construct($name, $isRequired, $valueType);
    }

    public function getValuesAcceptable(): ?array
    {
        return $this->valuesAcceptable;
    }

    public function validate(Parameter $parameter): PrimitiveResponseInterface
    {
        $response = new Response();

        $parameterValue = $parameter->getValue();

        if (isset($this->valueType)) {
            if ($this->validateValueType($parameterValue)) {
                $response->addItem($parameter->getName(), $parameterValue);
            }
            return $response;
        }

        if (isset($this->valuesAcceptable)) {
            if (in_array($parameterValue, $this->valuesAcceptable)) {
                $response->addItem($parameter->getName(), $parameterValue);
            }
            return $response;
        }

        $response->addItem($parameter->getName(), $parameterValue);

        return $response;
    }

    protected function validateValueType($value): bool
    {
        return match ($this->valueType) {
            self::VALUE_TYPES_MAP[0] => (gettype((int)$value) === $this->valueType) ?? false,
            self::VALUE_TYPES_MAP[1] => (gettype((float)$value) === $this->valueType) ?? false,
            self::VALUE_TYPES_MAP[2],
            self::VALUE_TYPES_MAP[3] => (gettype($value) === $this->valueType) ?? false,
            self::VALUE_TYPES_MAP[4] => $this->validateDateType($value, self::DATE_FORMAT),
            default => false,
        };
    }

    protected function validateDateType(string $value, string $format = 'Y-m-d'): bool
    {
        $date = date_create($value);
        return (($date instanceof DateTime) && date_format($date, $format));
    }
}

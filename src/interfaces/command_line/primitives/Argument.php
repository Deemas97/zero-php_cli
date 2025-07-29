<?php
namespace App\Interfaces\CommandLine\Primitives;

include_once './src/interfaces/command_line/Primitive.php';
include_once './src/interfaces/command_line/primitives/Response.php';
include_once './src/interfaces/command_line/primitives/PrimitiveResponseInterface.php';

use App\Interfaces\CommandLine\Primitive;

class Argument extends Primitive
{
    protected ?array $valuesAcceptable = null;

    public function __construct(string $name, array $valuesAcceptable)
    {
        parent::__construct($name);

        $this->valuesAcceptable = $valuesAcceptable;
    }

    public function validate(): PrimitiveResponseInterface
    {
        return new Response();
    }
}
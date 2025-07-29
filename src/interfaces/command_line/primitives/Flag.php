<?php
namespace App\Interfaces\CommandLine\Primitives;

include_once './src/interfaces/command_line/Primitive.php';
include_once './src/interfaces/command_line/primitives/Response.php';
include_once './src/interfaces/command_line/primitives/PrimitiveResponseInterface.php';

use App\Interfaces\CommandLine\Primitive;

class Flag extends Primitive
{
    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    public function validate(): PrimitiveResponseInterface
    {
        return new Response();
    }
}
<?php
namespace App\Interfaces\CommandLine\Commands;

include_once './src/interfaces/command_line/Primitive.php';
include_once './src/interfaces/command_line/PrimitivesInterface.php';
include_once './src/interfaces/command_line/Primitives.php';
include_once './src/interfaces/command_line/commands/CommandResponseInterface.php';
include_once './src/services/command_line_decoder/Parameter.php';

use App\Interfaces\CommandLine\Primitives;
use App\Interfaces\CommandLine\Primitives\Argument;
use App\Interfaces\CommandLine\Primitives\Flag;
use App\Interfaces\CommandLine\Primitives\Option;
use App\Interfaces\CommandLine\PrimitivesInterface;

abstract class Command
{
    const HELP_INFO = [];

    // Подразделить на группы: КОМАНДА [ГРУППА_1][ГРУППА_2]...
    protected PrimitivesInterface $options;
    protected PrimitivesInterface $flags;
    protected PrimitivesInterface $arguments;

    public function __construct()
    {
        $this->options = new Primitives();
        $this->flags = new Primitives();
        $this->arguments = new Primitives();
    }

    abstract public function validate(array $parameters): CommandResponseInterface;

    public function addOptions(Option ...$options): void
    {
        $this->options->add($options);
    }

    public function addFlags(Flag ...$flags): void
    {
        $this->flags->add($flags);
    }

    public function addArguments(Argument ...$arguments): void
    {
        $this->flags->add($arguments);
    }
}
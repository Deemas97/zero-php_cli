<?php
namespace App\Interfaces\CommandLine;

include_once './src/interfaces/command_line/Command.php';
include_once './src/interfaces/command_line/CommandLineSpecificationInterface.php';

use App\Interfaces\CommandLine\Commands\Command;

class CommandLineSpecification implements CommandLineSpecificationInterface
{
    private CommandsInterface $commands;

    public function __construct()
    {
        $this->commands = new Commands();
    }

    public function getCommands(): CommandsInterface
    {
        return $this->commands;
    }

    public function addCommand(string $commandName, Command $command)
    {
        $this->commands->add($commandName, $command);
    }
}

<?php
namespace App\Interfaces\CommandLine;

include_once './src/interfaces/command_line/CommandsInterface.php';
include_once './src/interfaces/command_line/Command.php';

use App\Interfaces\CommandLine\Commands\Command;

class Commands implements CommandsInterface
{
    private array $commands = [];

    public function get(string $name): ?Command
    {
        return $this->commands[$name] ?? null;
    }

    public function getAll(): array
    {
        return $this->commands;
    }

    public function add(string $name, Command $command): void
    {
        $this->commands[$name] = $command;
    }

    public function reset():void
    {
        $this->commands = [];
    }
}
<?php
namespace App\Interfaces\CommandLine;

include_once './src/interfaces/command_line/Command.php';

use App\Interfaces\CommandLine\Commands\Command;

interface CommandsInterface
{
    public function get(string $name): ?Command;

    public function getAll(): array;

    public function add(string $name, Command $command): void;
}
<?php
namespace App\Interfaces\CommandLine;

interface CommandLineSpecificationInterface
{
    public function getCommands(): CommandsInterface;
}
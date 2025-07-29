<?php
namespace App\Services;

include_once './src/core/ServiceInterface.php';

use App\Core\ServiceInterface;
use App\Interfaces\CommandLine\Commands;

class CommandBuilder implements ServiceInterface
{
    public function prepareCommands(array $data = []): Commands
    {
        $commandsList = new Commands();

        return $commandsList;
    }
}
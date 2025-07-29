<?php
namespace App\Interfaces\CommandLine\Commands;

include_once './src/interfaces/command_line/Command.php';
include_once './src/interfaces/command_line/commands/Response.php';
include_once './src/interfaces/command_line/commands/CommandResponseInterface.php';

class Quit extends Command
{
    const HELP_INFO = [
        'command_name' => 'quit',
        'command_description' => 'Quit from App'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function validate(array $parameters): CommandResponseInterface
    {
        $response = new Response();

        $response->addItem('route', 'quit');

        return $response;
    }
}
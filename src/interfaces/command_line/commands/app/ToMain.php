<?php
namespace App\Interfaces\CommandLine\Commands;

include_once './src/interfaces/command_line/Command.php';
include_once './src/interfaces/command_line/commands/Response.php';
include_once './src/interfaces/command_line/commands/CommandResponseInterface.php';
include_once './src/interfaces/command_line/Command.php';
include_once './src/services/command_line_decoder/Parameter.php';

class ToMain extends Command
{
    const HELP_INFO = [
        'command_name' => 'to_main',
        'command_description' => 'Back to main page'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function validate(array $parameters): CommandResponseInterface
    {
        $response = new Response();

        $response->addItem('route', 'main');

        return $response;
    }
}
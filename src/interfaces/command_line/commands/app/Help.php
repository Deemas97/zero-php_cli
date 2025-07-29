<?php
namespace App\Interfaces\CommandLine\Commands;

include_once './src/interfaces/command_line/Command.php';
include_once './src/interfaces/command_line/commands/Response.php';
include_once './src/interfaces/command_line/commands/CommandResponseInterface.php';
include_once './src/interfaces/command_line/Command.php';
include_once './src/services/command_line_decoder/Parameter.php';

class Help extends Command
{
    const HELP_INFO = [
        'command_name' => 'help',
        'command_description' => 'Print acceptable commands list'
    ];

    protected array $helpInfoList = [];

    public function __construct(array $helpInfoList)
    {
        parent::__construct();

        $helpInfoList[] = self::HELP_INFO;
        $this->helpInfoList = $helpInfoList;
    }

    public function validate(array $parameters): CommandResponseInterface
    {
        $this->printHelp();

        return new Response();
    }

    protected function printHelp(): void
    {
        print "\n---help info----------------------------------------------------------------------------------------\n";

        foreach ($this->helpInfoList as $helpInfoCommand) {
            if (empty($helpInfoCommand)) {
                continue;
            }
            print "{$helpInfoCommand['command_name']}:\t\t\t{$helpInfoCommand['command_description']}\n";

            if (isset($helpInfoCommand['command_options'])) {
                foreach ($helpInfoCommand['command_options'] as $line) {
                    print "\t\t$line";
                }
            }

            print "\n";
        }
    }
}
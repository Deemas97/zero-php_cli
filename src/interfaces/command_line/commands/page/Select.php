<?php
namespace App\Interfaces\CommandLine\Commands;

include_once './src/interfaces/command_line/Command.php';
include_once './src/interfaces/command_line/commands/Response.php';
include_once './src/interfaces/command_line/commands/CommandResponseInterface.php';
include_once './src/interfaces/command_line/primitives/Option.php';
include_once './src/services/command_line_decoder/Parameter.php';

class Select extends Command
{
    const HELP_INFO = [
        'command_name' => 'select',
        'command_description' => 'Activate page element'
    ];

    const SELECTABLE_TYPES = [
        'link' => 'route'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function validate(array $parameters): CommandResponseInterface // replace method response to Event
    {
        $response = new Response();

        foreach ($this->options->getAll() as $option) {
            foreach ($parameters as $index => $parameter) {
                if ($parameter->getType() !== 'option') {
                    return $response;
                }

                $optionName = $option->getName();
                if ($optionName === $parameter->getName()) {
                    $response->addItem(self::SELECTABLE_TYPES[$optionName], $option->validate($parameter)->get($optionName));
                    unset($parameters[$index]);
                    break 2;
                }
            }
        }

        if (!empty($parameters)) {
            $response->reset();
        }

        return $response;
    }
}

<?php
namespace App\Interfaces\CommandLine\Commands;

include_once './src/interfaces/command_line/Command.php';
include_once './src/interfaces/command_line/commands/Response.php';
include_once './src/interfaces/command_line/commands/CommandResponseInterface.php';
include_once './src/interfaces/command_line/primitives/Option.php';
include_once './src/services/command_line_decoder/Parameter.php';

class Run extends Command
{
    const HELP_INFO = [
        'command_name' => 'run',
        'command_description' => 'Run executable component'
    ];

    const EXECUTABLE_TYPES = [
        'component' => 'route'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function validate(array $parameters): CommandResponseInterface
    {
        $response = new Response();

        $parametersGroup = [];

        foreach ($this->options->getAll() as $option) {
            foreach ($parameters as $index => $parameter) {
                if ($parameter->getType() !== 'option') {
                    return new Response();
                }

                $optionName = $option->getName();

                if (($optionName === $parameter->getName()) && ($optionName === 'component')) {
                    $response->addItem(self::EXECUTABLE_TYPES[$optionName], $option->validate($parameter)->get($optionName));
                }

                if ($optionName === $parameter->getName()) {
                    $optionResponse = $option->validate($parameter);

                    if ((($optionValidated = $optionResponse->get($optionName)) !== null)) {
                        $parametersGroup[$optionName] = $optionValidated;
                        unset($parameters[$index]);
                    } else {
                        return new Response();
                    }
                }
            }
        }

        unset($parametersGroup['component']);
        $response->addItem('parameters', $parametersGroup);

        if (!empty($parameters)) {
            $response->reset();
        }

        return $response;
    }
}

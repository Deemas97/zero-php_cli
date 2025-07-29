<?php
namespace App\Services;

include_once './src/core/ServiceInterface.php';
include_once './src/interfaces/command_line/CommandLineInterface.php';
include_once './src/core/events/Events.php';
include_once './src/core/events/EventsInterface.php';
include_once './src/services/CommandLineDecoder.php';
include_once './src/interfaces/command_line/commands/app/Quit.php';
include_once './src/interfaces/command_line/commands/app/ToMain.php';
include_once './src/interfaces/command_line/commands/app/Help.php';
include_once './src/interfaces/command_line/Commands.php';
include_once './src/interfaces/command_line/Response.php';

use App\Core\Events;
use App\Core\EventsInterface;
use App\Core\ServiceInterface;
use App\Core\StoreTempProxy;
use App\Interfaces\CommandLine\CommandLineSpecificationInterface;
use App\Interfaces\CommandLine\Commands;
use App\Interfaces\CommandLine\Commands\Help;
use App\Interfaces\CommandLine\Commands\Quit;
use App\Interfaces\CommandLine\Commands\ToMain;

class InterfaceHandler implements ServiceInterface
{
    protected Commands $clCommands;

    public function __construct(
        protected StoreTempProxy $temp,
        protected CommandLineDecoder $clDecoder,
    )
    {}

    public function process(CommandLineSpecificationInterface $clSpecification = null): EventsInterface
    {
        $this->prepareInterface($clSpecification);

        $events = new Events();

        $breakLoop = false;
        for (; $breakLoop !== true;) {
            $clParameters = $this->clDecoder->decodeFromKeyboard();

            if ($clParameters === null) {
                continue;
            }

            $clCommandParameter = $clParameters->get(0);

            if ($clCommandParameter === null) {
                continue;
            }

            if ($clCommandParameter->getType() !== 'command') {
                continue;
            }

            $command = $this->clCommands->get($clCommandParameter->getName());

            if ($command === null) {
                continue;
            }

            $clParametersArray = $clParameters->getAll();
            unset($clParametersArray[0]);

            $commandResponse = $command->validate($clParametersArray);

            if ($route = $commandResponse->get('route')) {
                $events->addItem('route', $route);
            }

            if ($quit = $commandResponse->get('quit')) {
                $events->addItem('quit', $quit);
            }

            if ($parameters = $commandResponse->get('parameters')) {
                $events->addItem('parameters', $parameters);
            }

            if (!empty($events->getAll()) && $events->get('error') === null) {
                $breakLoop = true;
            }
        }

        return $events;
    }

    protected function prepareInterface(CommandLineSpecificationInterface $clSpecification = null): void
    {
        $this->clCommands = new Commands();

        if (isset($clSpecification)) {
            $this->clCommands = $clSpecification->getCommands();
        }

        $this->clCommands->add('quit', new Quit());
        $this->clCommands->add('to_main', new ToMain());

        $helpInfoList = [];
        foreach ($this->clCommands->getAll() as $clCommand) {
            $helpInfoList[] = $clCommand::HELP_INFO;
        }

        $this->clCommands->add('help', new Help($helpInfoList));
    }
}

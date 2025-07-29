<?php
namespace App\Core;

include_once './src/core/ModuleInterface.php';
include_once './src/core/actions/Actions.php';
include_once './src/core/modules/ActionManagerInterface.php';
include_once './src/core/response/Response.php';
include_once './src/services/ControllerHandler.php';

use App\Services\ControllerHandler;

class ActionManager implements ModuleInterface, ActionManagerInterface
{
    public function __construct(
        private readonly ControllerHandler $controllerHandler
    )
    {}

    public function process(MessageBusInterface $messageBus): ResponseInterface
    {
        $response = new Response();

        if (!$controllerName = $messageBus->get('controller')) {
            return $response;
        }

        if (!$methodName = $messageBus->get('method')) {
            return $response;
        }

        $controllerMethodParameters = $messageBus->get('controller_method_parameters');

        $controllerHandlerResponse = $this->controllerHandler->run($controllerName, $methodName, $controllerMethodParameters);

        $controllerHandlerResponseDump = $controllerHandlerResponse->getAll();

        $response->addItem('quit', $controllerHandlerResponseDump['quit'] ?? false);
        $response->addItem('route', $controllerHandlerResponseDump['route'] ?? ':cli');
        $response->addItem('parameters', $controllerHandlerResponseDump['controller_method'] ?? []);

        return $response;
    }
}

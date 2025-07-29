<?php
namespace App\Core;

include_once './src/core/ModuleInterface.php';
include_once './src/core/modules/EventsManagerInterface.php';
include_once './src/core/events/EventsInterface.php';
include_once './src/core/actions/ActionsInterface.php';
include_once './src/core/request/RequestInterface.php';
include_once './src/core/response/Response.php';
include_once './src/core/response/ResponseInterface.php';

class EventsManager implements ModuleInterface, EventsManagerInterface
{
    const REQUEST_SCHEMA = [
        'route',
        'quit',
        'parameters'
    ];

    const ACTIONS_SCHEMA = [
        'controller',
        'method',
        'controller_method_parameters'
    ];

    const RESPONSE_SCHEMA = [
        'route',
        'quit',
        'controller',
        'parameters'
    ];

    const ROUTE_DEFAULT = ':cli';

    protected RequestInterface $requestTemp;
    protected ActionsInterface $actionsTemp;
    protected ResponseInterface $responseTemp;

    public function __construct()
    {
        $this->requestTemp = $this->initRequest();
        $this->actionsTemp = $this->initActions();
        $this->responseTemp = $this->initResponse();
    }

    public function initMessageBus(): MessageBusInterface
    {
        $request = new Request();

        $request->addItem('route', 'main');
        $request->addItem('parameters', [
            'controller_method' => [
                'testParam' => 'YEAH!'
            ]
        ]);

        return $request;
    }

    public function process(MessageBusInterface $messageBus): MessageBusInterface
    {
        return match (true) {
            ($messageBus instanceof RequestInterface) => $this->handleRequest($messageBus),
            ($messageBus instanceof ActionsInterface) => $this->handleActions($messageBus),
            ($messageBus instanceof ResponseInterface) => $this->handleResponse($messageBus),
            default => new Response()
        };
    }

    public function getRequest(): RequestInterface
    {
        return $this->requestTemp;
    }

    public function getActions(): ActionsInterface
    {
        return $this->actionsTemp;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->responseTemp;
    }

    // input parameter: array
    protected function handleRequest(RequestInterface $requestRaw): RequestInterface
    {
        $response = new Request();

        $requestDump = $requestRaw->getAll();

        foreach (self::REQUEST_SCHEMA as $type) {
            if (isset($requestDump[$type])) {
                if ($type === self::REQUEST_SCHEMA[0]) {
                    $response->addItem(self::REQUEST_SCHEMA[0], $requestDump[$type] ?? self::ROUTE_DEFAULT);
                }

                if ($type === self::REQUEST_SCHEMA[1]) {
                    $response->addItem(self::REQUEST_SCHEMA[1], boolval($requestDump[$type]));
                }

                if ($type === self::REQUEST_SCHEMA[2]) {
                    $response->addItem(self::REQUEST_SCHEMA[2], $requestDump[$type] ?? []);
                }
            }
        }

        $this->requestTemp = $response;

        return $response;
    }

    protected function handleActions(ActionsInterface $actionsRaw): ActionsInterface
    {
        $response = new Actions();

        $actionsDump = $actionsRaw->getAll();

        foreach (self::ACTIONS_SCHEMA as $type) {
            if (isset($actionsDump[$type])) {
                if ($type === self::ACTIONS_SCHEMA[0]) {
                    $response->addItem(self::ACTIONS_SCHEMA[0], $actionsDump[$type]);
                }

                if ($type === self::ACTIONS_SCHEMA[1]) {
                    $response->addItem(self::ACTIONS_SCHEMA[1], $actionsDump[$type]);
                }

                if ($type === self::ACTIONS_SCHEMA[2]) {
                    $response->addItem(self::ACTIONS_SCHEMA[2], $actionsDump[$type] ?? []);
                }
            }
        }

        $this->actionsTemp = $response;

        return $response;
    }

    protected function handleResponse(ResponseInterface $responseRaw): ResponseInterface
    {
        $response = new Response();

        $responseDump = $responseRaw->getAll();

        foreach (self::RESPONSE_SCHEMA as $type) {
            if (isset($responseDump[$type])) {
                if ($type === self::RESPONSE_SCHEMA[0]) {
                    $response->addItem(self::RESPONSE_SCHEMA[0], $responseDump[$type] ?? self::ROUTE_DEFAULT);
                }

                if ($type === self::RESPONSE_SCHEMA[1]) {
                    $response->addItem(self::RESPONSE_SCHEMA[1], boolval($responseDump[$type]));
                }

                if ($type === self::RESPONSE_SCHEMA[2]) {
                    $response->addItem(self::RESPONSE_SCHEMA[2], $responseDump[$type] ?? []);
                }
            }
        }

        $this->responseTemp = $response;

        return $response;
    }

    protected function initRequest(): RequestInterface
    {
        $request = new Request();

        foreach (self::REQUEST_SCHEMA as $index => $parameterName) {
            $request->addItem($index, $parameterName);
        }

        return $request;
    }

    protected function initActions(): ActionsInterface
    {
        $actions = new Actions();

        foreach (self::ACTIONS_SCHEMA as $index => $parameterName) {
            $actions->addItem($index, $parameterName);
        }

        return $actions;
    }

    protected function initResponse(): ResponseInterface
    {
        $response = new Response();

        foreach (self::RESPONSE_SCHEMA as $index => $parameterName) {
            $response->addItem($index, $parameterName);
        }

        return $response;
    }
}

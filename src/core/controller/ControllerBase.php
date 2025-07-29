<?php
namespace App\Core;

include_once './src/core/events/EventsInterface.php';
include_once './src/core/controller/ControllerInterface.php';
include_once './src/core/controller/ControllerResponseInterface.php';
include_once './src/services/InterfaceHandler.php';
include_once './src/services/Window.php';

use App\Core\Controller\ControllerInterface;
use App\Core\Controller\ControllerResponseInterface;
use App\Core\Controller\Response;
use App\Services\Window;

abstract class ControllerBase implements ControllerInterface
{
    public function __construct(
        protected Window $window,
    )
    {}

    protected function initResponse(EventsInterface $events = null): ControllerResponseInterface
    {
        return isset($events) ? $this->createResponse($events) : $this->createResponseDefault();
    }

    private function createResponse(EventsInterface $events): ControllerResponseInterface
    {
        $response = new Response();

        $response->addItem('quit', $events->get('quit'));
        $response->addItem('route', $events->get('route'));

        $parameters = [];
        if (is_array($eventsParameters = $events->get('parameters'))) {
            foreach ($eventsParameters as $name => $value) {
                $parameters[$name] = $value;
            }
        }

        $response->addItem('parameters', $parameters);

        return $response;
    }

    private function createResponseDefault(): ControllerResponseInterface
    {
        $response = new Response();

        $response->addItem('quit',  false);
        $response->addItem('route',  ':cli');
        $response->addItem('parameters', []);

        return $response;
    }
}

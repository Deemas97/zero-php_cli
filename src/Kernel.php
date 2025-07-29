<?php
namespace App;

include_once './src/core/container/Container.php';
include_once './src/core/container/ContainerProxy.php';
include_once './src/core/modules/ActionManager.php';
include_once './src/core/modules/EventsManager.php';
include_once './src/core/modules/ResponseManager.php';
include_once './src/core/modules/RequestManager.php';
include_once './src/core/request/RequestInterface.php';
include_once './src/core/request/Request.php';
include_once './src/core/store/StoreTemp.php';
include_once './src/core/store/StoreTempProxy.php';

use App\Core\ActionManager;
use App\Core\ActionManagerInterface;
use App\Core\Container as GlobalContainer;
use App\Core\ContainerProxy;
use App\Core\EventsManager;
use App\Core\EventsManagerInterface;
use App\Core\GlobalContainerInterface;
use App\Core\ModuleInterface;
use App\Core\ResponseManager;
use App\Core\ResponseManagerInterface;
use App\Core\RequestManager;
use App\Core\RequestManagerInterface;
use App\Core\StoreInterface;
use App\Core\StoreTemp;
use App\Core\StoreTempProxy;

class Kernel
{
    protected ?GlobalContainerInterface $container = null;
    protected StoreInterface $temp;
    protected ModuleInterface&EventsManagerInterface $eventsManager;
    protected ModuleInterface&RequestManagerInterface $requestManager;
    protected ModuleInterface&ActionManagerInterface $actionManager;
    protected ModuleInterface&ResponseManagerInterface $responseManager;

    private bool $isBooted = false;

    public function __construct(){}
    
    public function run(): void
    {
        $this->boot();

        $eventManagerResponse = $this->eventsManager->initMessageBus();

        for (; $eventManagerResponse->get('quit') !== true;) {
            $actions = $this->requestManager->process($eventManagerResponse);

            $eventManagerResponse = $this->eventsManager->process($actions);
            if ($eventManagerResponse->get('error')) {
                continue;
            }

            $response = $this->actionManager->process($eventManagerResponse);

            $eventManagerResponse = $this->eventsManager->process($response);
            if ($eventManagerResponse->get('error')) {
                continue;
            }

            $request = $this->responseManager->process($response);
            $eventManagerResponse = $this->eventsManager->process($request);
            if ($eventManagerResponse->get('error')) {
                break;
            }
        }

        $this->quit();
    }

    private function boot(): void
    {
        $this->container = new GlobalContainer();
        $this->container->set(ContainerProxy::class, new ContainerProxy($this->container));

        $this->temp = $this->container->get(StoreTemp::class);
        $this->container->set(StoreTempProxy::class, new StoreTempProxy($this->temp));

        $this->eventsManager = $this->container->get(EventsManager::class);
        $this->requestManager = $this->container->get(RequestManager::class);
        $this->actionManager = $this->container->get(ActionManager::class);
        $this->responseManager = $this->container->get(ResponseManager::class);

        $this->isBooted = true;
    }

    private function reboot(): void
    {
        $this->isBooted = false;

        $this->boot();
    }

    private function quit(): void
    {
        if ($this->isBooted === false) {
            return;
        }

        $this->isBooted = false;
        $this->container = null;

        print "\n";
    }
}

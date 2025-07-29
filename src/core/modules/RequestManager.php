<?php
namespace App\Core;

include_once './src/core/ModuleInterface.php';
include_once './src/core/modules/RequestManagerInterface.php';
include_once './src/core/controller/ControllerCLI.php';
include_once './src/controllers/MainController.php';
include_once './src/controllers/ConversionsController.php';
include_once './src/controllers/ConversionsFixController.php';
include_once './src/controllers/ConversionsConvertToUsdController.php';
include_once './src/controllers/OffersFixController.php';
include_once './src/controllers/OffersFixDescriptionController.php';
include_once './src/controllers/OffersFixSmartlinkOffersController.php';
include_once './src/controllers/QuitController.php';
include_once './src/core/actions/Actions.php';
include_once './src/services/Router.php';
include_once './src/services/router/RouteInterface.php';

use App\Controllers\ConversionsController;
use App\Controllers\ConversionsConvertToUsdController;
use App\Controllers\ConversionsFixController;
use App\Controllers\MainController;
use App\Controllers\OffersFixController;
use App\Controllers\OffersFixDescriptionController;
use App\Controllers\OffersFixSmartlinkOffersController;
use App\Controllers\QuitController;
use App\Services\Router;
use App\Services\Router\RouteInterface;

class RequestManager implements ModuleInterface, RequestManagerInterface
{
    public function __construct(
        protected Router $router
    )
    {
        $this->initRouter();
    }

    public function process(MessageBusInterface $messageBus): ActionsInterface
    {
        $requestParameters = $messageBus->get('parameters');
        $requestRoute = $messageBus->get('route');

        $routerResponse = $this->router->resolve($requestRoute);
        $route = $routerResponse->get('route');

        $actions = new Actions();

        if ($route instanceof RouteInterface) {
            $methodParameters = $requestParameters['controller_method'] ?? [];

            $actions->addItem('controller', $route->getControllerName());
            $actions->addItem('method', $route->getMethodName());
            $actions->addItem('controller_method_parameters', $methodParameters);
        }

        return $actions;
    }

    protected function initRouter(): void
    {
        $this->router
            ->register(':cli', ControllerCLI::class, 'listen')

            ->register('main', MainController::class, 'index')
            ->register('conversions', ConversionsController::class, 'index')
            ->register('fix_conversions', ConversionsFixController::class, 'index')
            ->register('convert_to_usd', ConversionsConvertToUsdController::class, 'index')
            ->register('offers_fix', OffersFixController::class, 'index')
            ->register('fix_offers_descriptions', OffersFixDescriptionController::class, 'index')
            ->register('fix_smartlinks_offer', OffersFixSmartlinkOffersController::class, 'index')
            ->register('quit', QuitController::class, 'quit');
    }
}

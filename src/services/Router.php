<?php
namespace App\Services;

include_once './src/core/actions/Actions.php';
include_once './src/core/request/Request.php';
include_once './src/core/ServiceInterface.php';
include_once './src/services/router/Route.php';
include_once './src/services/router/Routes.php';

use App\Core\Response;
use App\Core\ResponseInterface;
use App\Core\ServiceInterface;
use App\Services\Router\Route;
use App\Services\Router\Routes;

class Router implements ServiceInterface
{
    public function __construct(
        private readonly Routes $routes
    )
    {}

    public function register(string $routeName, string $controllerName, string $methodName): self
    {
        $this->routes->set($routeName, new Route($controllerName, $methodName));

        return $this;
    }

    public function resolve(string $routeId): ResponseInterface
    {
        $route = $this->routes->has($routeId) ? $this->routes->get($routeId) : null;

        $response = new Response();

        $response->addItem('route', $route);

        return $response;
    }

    public function getRoutes(): Routes
    {
        return $this->routes;
    }
}

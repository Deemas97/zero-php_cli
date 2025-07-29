<?php
namespace App\Services\Router;

include_once './src/services/router/RouteInterface.php';
include_once './src/services/router/RoutesInterface.php';

class Routes implements RoutesInterface
{
    private array $routes = [];

    public function get(string $id): ?RouteInterface
    {
        return $this->has($id) ? $this->routes[$id] : null;
    }

    public function has(string $id): bool
    {
        return isset($this->routes[$id]);
    }

    public function set(string $id, RouteInterface $route): void
    {
        $this->routes[$id] = $route;
    }
}

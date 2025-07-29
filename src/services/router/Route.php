<?php
namespace App\Services\Router;

include_once './src/services/router/RouteInterface.php';

class Route implements RouteInterface
{
    public function __construct(
        private readonly string $controllerName,
        private readonly string $methodName
    )
    {}

    public function getControllerName(): string
    {
        return $this->controllerName;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }
}

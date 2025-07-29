<?php
namespace App\Services;

include_once './src/core/ServiceInterface.php';
include_once './src/core/container/ContainerProxy.php';
include_once './src/services/controller_handler/Response.php';

use App\Core\ContainerProxy;
use App\Core\ServiceInterface;
use App\Services\ControllerHandler\Response;
use App\Core\ResponseInterface;
use ErrorException;
use ReflectionClass;
use ReflectionException;

class ControllerHandler implements ServiceInterface
{
    public function __construct(private readonly ContainerProxy $container)
    {}

    public function run(string $controllerName, string $methodName, array $parameters = []): ResponseInterface
    {
        $response = new Response();

        $controller = $this->container->get($controllerName);

        try {
            $controllerClassReflector = new ReflectionClass($controller::class);
        } catch (ReflectionException $e) {
            return $response;
        }

        try {
            $controllerMethodReflector = $controllerClassReflector->getMethod($methodName);
        } catch (ReflectionException $e) {
            return $response;
        }

        $controllerMethodParameters = $controllerMethodReflector->getParameters() ?? [];
        $controllerMethodParametersBonded = [];

        foreach ($controllerMethodParameters as $controllerMethodParameter) {
            $controllerMethodParameterType = $controllerMethodParameter->getType()->getName();

            if ($controllerMethodParameterType === 'bool') {
                $controllerMethodParameterType = 'boolean';
            }

            if ($controllerMethodParameterType === 'float') {
                $controllerMethodParameterType = 'double';
            }

            $controllerMethodParameterName = $controllerMethodParameter->getName();

            if (isset($parameters[$controllerMethodParameterName])) {
                if (($parameters[$controllerMethodParameterName] instanceof $controllerMethodParameterType)
                    || (gettype($parameters[$controllerMethodParameterName]) === $controllerMethodParameterType)) {
                    $controllerMethodParametersBonded[] = $parameters[$controllerMethodParameterName];
                } else {
                    return $response;
                }
            }
        }

        try {
            if (empty($controllerMethodParametersBonded)) {
                $controllerResponse = $controller->$methodName();
            } else {
                $controllerResponse = $controller->$methodName(...$controllerMethodParametersBonded);
            }
        } catch (ErrorException $e) {
            return $response;
        }

        $controllerResponseDump = $controllerResponse->getAll();

        $controllerData = [];
        foreach ($controllerResponseDump['parameters'] as $index => $item) {
            $controllerData[$index] = $item;
        }

        $response->addItem('quit', $controllerResponseDump['quit']);
        $response->addItem('route', $controllerResponseDump['route']);
        $response->addItem('controller_method', $controllerData);

        return $response;
    }
}

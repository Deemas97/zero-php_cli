<?php
namespace App\Core\Actions;

include_once './src/core/actions/MethodSpecification.php';
include_once './src/core/actions/ParametersSpecification.php';

class ControllerSpecification
{
    protected ?string $name;
    protected ?MethodSpecification $method;
    protected ?ParametersSpecification $parameters;

    public function __construct(array $controllerData)
    {
        $this->name = $controllerData['name'] ?? null;
        $this->method = $this->prepareMethod($controllerData['method'] ?? null);

        // ControllerHandler parameters
        $this->parameters = $this->prepareParameters($controllerData['parameters'] ?? null);
        ////

    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMethod(): MethodSpecification
    {
        return $this->method;
    }

    public function getParameters(): ParametersSpecification
    {
        return $this->parameters;
    }

    private function prepareMethod(array $method): MethodSpecification
    {
        return new MethodSpecification($method);
    }

    private function prepareParameters(array $parameters = []): ParametersSpecification
    {
        return new ParametersSpecification($parameters);
    }
}

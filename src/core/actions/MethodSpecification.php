<?php
namespace App\Core\Actions;

class MethodSpecification
{
    private string $name;
    private ParametersSpecification $methodParameters;
    private ParametersSpecification $parameters;

    public function __construct(array $methodData)
    {
        $this->name = $methodData['name'] ?? null;
        $this->methodParameters = new ParametersSpecification($methodData['method_parameters']);

        // Controller meta-parameters
        $this->parameters = new ParametersSpecification($methodData['parameters']);
        ////
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMethodParametersSpecification(): ParametersSpecification
    {
        return $this->methodParameters;
    }

    public function getParametersSpecification(): ParametersSpecification
    {
        return $this->parameters;
    }
}

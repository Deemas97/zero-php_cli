<?php
namespace App\Core;

include_once './src/core/container/GlobalContainerInterface.php';

use ReflectionClass;
use ReflectionException;

class Container implements GlobalContainerInterface
{
    protected array $components = [];

    public function get(string $id)
    {
        if ($this->has($id)) {
            return is_object($this->components[$id]) ? $this->components[$id] : $this->initComponent($this->components[$id]);
        }

        return $this->initComponent($id);
    }

    public function has(string $id): bool
    {
        return isset($this->components[$id]);
    }

    public function set(string $id, $item): void
    {
        $this->components[$id] = $item;
    }

    protected function initComponent(string $id): ?object
    {
        $component = $this->prepareComponent($id);

        if (is_object($component)) {
            $this->set($id, $component);
        }

        return $component;
    }

    protected function prepareComponent(string $id): ?object
    {
        try {
            $classReflector = new ReflectionClass($id);
        } catch (ReflectionException $e) {
            return null;
        }

        $constructReflector = $classReflector->getConstructor();
        if (empty($constructReflector)) {
            return new $id;
        }

        $constructParameters = $constructReflector->getParameters();
        if (empty($constructParameters)) {
            return new $id;
        }

        $parameters = [];
        foreach ($constructParameters as $parameter) {
            $parameterType = $parameter->getType()->getName();
            $parameters[$parameter->getName()] = $this->get($parameterType);
        }

        return new $id(...$parameters);
    }
}

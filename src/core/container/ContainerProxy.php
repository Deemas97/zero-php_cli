<?php
namespace App\Core;

include_once './src/core/container/GlobalContainerInterface.php';
include_once './src/core/container/Container.php';

class ContainerProxy implements GlobalContainerInterface
{
    public function __construct(
        private readonly Container $container
    )
    {}

    public function get(string $id)
    {
        return $this->container->get($id);
    }

    public function has(string $id): bool
    {
        return $this->container->has($id);
    }

    public function set(string $id, $item): void
    {
        $this->container->set($id, $item);
    }
}

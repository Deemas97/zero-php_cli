<?php
namespace App\Services\Router;

include_once './src/core/container/ContainerInterface.php';
include_once './src/services/router/RouteInterface.php';

use App\Core\ContainerInterface;

interface RoutesInterface extends ContainerInterface
{
    public function set(string $id, RouteInterface $route): void;
}

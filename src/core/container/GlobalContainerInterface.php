<?php
namespace App\Core;

include_once './src/core/container/ContainerInterface.php';

interface GlobalContainerInterface extends ContainerInterface
{
    public function set(string $id, $item): void;
}
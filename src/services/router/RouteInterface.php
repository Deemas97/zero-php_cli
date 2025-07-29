<?php
namespace App\Services\Router;

interface RouteInterface
{
    public function getControllerName(): string;
    public function getMethodName(): string;
}

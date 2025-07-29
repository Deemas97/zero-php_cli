<?php
namespace App\Core;

include_once './src/core/MessageBusInterface.php';
include_once './src/core/request/RequestInterface.php';
include_once './src/core/actions/ActionsInterface.php';
include_once './src/core/response/ResponseInterface.php';

interface EventsManagerInterface
{
    public function initMessageBus(): MessageBusInterface;
    public function getRequest(): RequestInterface;
    public function getActions(): ActionsInterface;
    public function getResponse(): ResponseInterface;
}

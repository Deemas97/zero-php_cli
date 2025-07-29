<?php
namespace App\Core;

include_once './src/core/MessageBusInterface.php';

interface ModuleInterface
{
    public function process(MessageBusInterface $messageBus): MessageBusInterface;
}

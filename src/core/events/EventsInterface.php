<?php
namespace App\Core;

include_once './src/core/MessageBusInterface.php';

interface EventsInterface
{
    public function get(string $id);
    public function getAll();
    public function addItem(string $id, $event): void;
}

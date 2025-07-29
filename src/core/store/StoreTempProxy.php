<?php
namespace App\Core;

include_once './src/core/ServiceInterface.php';
include_once './src/core/store/StoreInterface.php';
include_once './src/core/store/StoreTemp.php';

class StoreTempProxy implements ServiceInterface, StoreInterface
{
    public function __construct(
        protected StoreTemp $temp
    )
    {}

    public function get(string $id)
    {
        return $this->temp->get($id) ?? null;
    }

    public function getAll(): array
    {
        return $this->temp->getAll();
    }

    public function addItem(string $id, $value): void
    {
        $this->temp->addItem($id, $value);
    }
}

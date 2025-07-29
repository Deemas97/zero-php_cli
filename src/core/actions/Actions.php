<?php
namespace App\Core;

include_once './src/core/actions/ActionsInterface.php';
include_once './src/core/actions/ControllerSpecification.php';
include_once './src/core/actions/ParametersSpecification.php';

use App\Core\Actions\ControllerSpecification;
use App\Core\Actions\ParametersSpecification;

class Actions implements ActionsInterface
{
    private array $data = [];

    public function get(string $id)
    {
        return $this->data[$id] ?? null;
    }

    public function getAll(): array
    {
        return $this->data;
    }

    public function addItem(string $id, $value): void
    {
        $this->data[$id] = $value;
    }

    public function reset():void
    {
        $this->data = [];
    }
}

<?php
namespace App\Services\TemplateBuilder;

include_once './src/core/view/ViewInterface.php';
include_once './src/interfaces/command_line/CommandLineSpecificationInterface.php';

use App\Core\ViewInterface;
use App\Interfaces\CommandLine\CommandLineSpecificationInterface;

interface TemplateInterface
{
    public function setData(array $data): void;
    public function getView(): ViewInterface;
    public function getInterfaceSpecification(array $data = []): ?CommandLineSpecificationInterface;
}

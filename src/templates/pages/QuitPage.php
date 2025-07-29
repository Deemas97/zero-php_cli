<?php
namespace App\Templates;

include_once './src/core/view/View.php';
include_once './src/core/view/ViewInterface.php';
include_once './src/services/template_builder/TemplateInterface.php';

use App\Core\View;
use App\Core\ViewInterface;
use App\Interfaces\CommandLine\CommandLineSpecification;
use App\Interfaces\CommandLine\CommandLineSpecificationInterface;
use App\Interfaces\CommandLine\Commands\Help;
use App\Interfaces\CommandLine\Commands\Select;
use App\Services\TemplateBuilder\TemplateInterface;

class QuitPage implements TemplateInterface
{
    private array $data = [];

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getView(): ViewInterface
    {
        $content = [];

        $content[] = "====================================================================================================";
        $content[] = "[DONE]";
        $content[] = "----------------------------------------------------------------------------------------------------\n";

        return new View($content);
    }

    public function getInterfaceSpecification(array $data = []): ?CommandLineSpecificationInterface
    {
        return null;
    }
}

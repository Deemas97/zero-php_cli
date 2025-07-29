<?php
namespace App\Templates;

include_once './src/core/view/View.php';
include_once './src/core/view/ViewInterface.php';
include_once './src/interfaces/command_line/CommandLineSpecification.php';
include_once './src/interfaces/command_line/CommandLineSpecificationInterface.php';
include_once './src/interfaces/command_line/commands/page/Select.php';
include_once './src/services/template_builder/TemplateInterface.php';

use App\Core\View;
use App\Core\ViewInterface;
use App\Interfaces\CommandLine\CommandLineSpecification;
use App\Interfaces\CommandLine\CommandLineSpecificationInterface;
use App\Interfaces\CommandLine\Commands\Select;
use App\Interfaces\CommandLine\Primitives\Option;
use App\Services\TemplateBuilder\TemplateInterface;

class MainMenu implements TemplateInterface
{
    const SELECTABLE_ITEMS_LIST = [
        'link' => [
            'conversions',
            'offers_fix'
        ]
    ];

    const SELECT_OPTIONS_MAP = [
        [
            'name' => null,
            'required' => false,
            'type' => 'string',
            'valuesList' => self::SELECTABLE_ITEMS_LIST
        ]
    ];

    private array $data = [];

    public function __construct()
    {
        foreach (self::SELECTABLE_ITEMS_LIST as $groupName => $items) {
            $this->data[$groupName] = $items;
        }
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getView(): ViewInterface
    {
        $content = [];

        $content[] = "====================================================================================================";
        $content[] = "<h1>AdminPanel</h1>";
        $content[] = "----------------------------------------------------------------------------------------------------\n";

        foreach ($this->data['link'] as $link) {
            $content[] = ">\x1B[38;5;82m[link]\x1B[38;5;255m\t<\x1B[1m{$link}\x1B[0m>";
        }


        $content[] = "\n";
        $content[] = "----------------------------------------------------------------------------------------------------";
        $content[] = "-+- Ads: Гейб Ньюэл заявил, что Half-Life 4 вот-вот...\x1B[38;5;82m[link]\x1B[38;5;255m <\x1B[1mexternal_1\x1B[0m>";
        $content[] = "----------------------------------------------------------------------------------------------------";
        $content[] = "(i) Чтобы отключить рекламу, удалите ее из кода";
        $content[] = "----------------------------------------------------------------------------------------------------";

        return new View($content);
    }

    public function getInterfaceSpecification(array $data = []): CommandLineSpecificationInterface
    {
        // CommandBuilder
        $commandSelect = new Select();
        foreach (self::SELECTABLE_ITEMS_LIST as $optionName => $optionValues) {
            foreach (self::SELECT_OPTIONS_MAP as $option) {
                $optionName = (string) $optionName ?? null;

                if (isset($optionName)) {
                    $optionIsRequired = $option['required'] ?? null;
                    $optionType = $option['type'] ?? null;
                    $optionValuesList = $option['valuesList'][$optionName] ?? null;
                    $commandSelect->addOptions(new Option($optionName, $optionIsRequired, $optionType, $optionValuesList));
                }
            }
        }
        ////

        $interfaceSpecification = new CommandLineSpecification();

        $interfaceSpecification->addCommand('select', $commandSelect);

        return $interfaceSpecification;
    }
}

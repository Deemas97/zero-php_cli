<?php
namespace App\Templates;

include_once './src/core/view/View.php';
include_once './src/core/view/ViewInterface.php';
include_once './src/interfaces/command_line/CommandLineSpecification.php';
include_once './src/interfaces/command_line/CommandLineSpecificationInterface.php';
include_once './src/interfaces/command_line/commands/app/Help.php';
include_once './src/interfaces/command_line/commands/page/Select.php';
include_once './src/interfaces/command_line/commands/page/Run.php';
include_once './src/services/template_builder/TemplateInterface.php';

use App\Core\View;
use App\Core\ViewInterface;
use App\Interfaces\CommandLine\CommandLineSpecification;
use App\Interfaces\CommandLine\CommandLineSpecificationInterface;
use App\Interfaces\CommandLine\Commands\Run;
use App\Interfaces\CommandLine\Commands\Select;
use App\Interfaces\CommandLine\Primitives\Option;
use App\Services\TemplateBuilder\TemplateInterface;

class OffersFix implements TemplateInterface
{
    const SELECTABLE_ITEMS_LIST = [
        'link' => [
            'main'
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

    const EXECUTABLE_ITEMS_LIST = [
        'fix_offers_descriptions' => [
            [
                'name' => 'component',
                'required' => true,
                'type' => 'string',
                'valuesList' => ['fix_offers_descriptions']
            ],
            [
                'name' => 'id',
                'required' => false,
                'type' => 'integer'
            ]
        ],

        'fix_smartlinks_offer' => [
            [
                'name' => 'component',
                'required' => true,
                'type' => 'string',
                'valuesList' => ['fix_smartlinks_offer']
            ],
            [
                'name' => 'id',
                'required' => false,
                'type' => 'integer'
            ]
        ]
    ];

    private array $data = [];

    public function __construct()
    {
        foreach (self::SELECTABLE_ITEMS_LIST as $groupName => $items) {
            $this->data[$groupName] = $items;
        }

        foreach (self::EXECUTABLE_ITEMS_LIST as $componentName => $item) {
            $this->data['component'][$componentName] = $item;
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
        $content[] = "<h1>Offers Fix</h1>";
        $content[] = "----------------------------------------------------------------------------------------------------\n";

        foreach ($this->data['link'] as $link) {
            $content[] = ">\x1B[38;5;82m[link]\x1B[38;5;255m\t<\x1B[1m{$link}\x1B[0m>";
        }

        $content[] = "\n";

        foreach (array_keys($this->data['component']) as $componentName) {
            $content[] = ">\x1B[38;5;27m[component]\x1B[38;5;255m\t<\x1B[1m{$componentName}\x1B[0m>";
        }

        $content[] = "\n";
        $content[] = "----------------------------------------------------------------------------------------------------";

        return new View($content);
    }

    public function getInterfaceSpecification(array $data = []): CommandLineSpecificationInterface
    {
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

        $commandRun = new Run();
        foreach (self::EXECUTABLE_ITEMS_LIST as $componentData) {
            foreach ($componentData as $option) {
                $optionName = (string)$option['name'] ?? null;

                if (isset($optionName)) {
                    $optionIsRequired = $option['required'] ?? null;
                    $optionType = $option['type'] ?? null;
                    $optionValuesList = $option['valuesList'] ?? null;
                    $commandRun->addOptions(new Option($optionName, $optionIsRequired, $optionType, $optionValuesList));
                }
            }
        }

        $interfaceSpecification = new CommandLineSpecification();

        $interfaceSpecification->addCommand('select', $commandSelect);
        $interfaceSpecification->addCommand('run', $commandRun);

        return $interfaceSpecification;
    }
}

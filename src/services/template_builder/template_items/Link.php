<?php
namespace App\Services\TemplateBuilder;

include_once './src/services/template_builder/TemplateItemInterface.php';

class Link implements TemplateItemInterface
{
    private string $title;
    private string $route;

    public function __construct(string $title, string $route)
    {
        $this->title = $title;
        $this->route = $route;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getView(): array
    {
        return ["\x1B[38;5;82m[link]\x1B[38;5;255m\t<\x1B[4m{$this->getTitle()}\x1B[24m>"];
    }
}

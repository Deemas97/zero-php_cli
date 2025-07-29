<?php
namespace App\Services\TemplateBuilder;

interface TemplateItemInterface
{
    public function getView(): array;
}
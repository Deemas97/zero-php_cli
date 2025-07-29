<?php
namespace App\Core;

interface ViewInterface
{
    public function getContent(): array;
    public function appendContent(ViewInterface $view): void;
}
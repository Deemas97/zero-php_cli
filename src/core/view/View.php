<?php
namespace App\Core;

include_once './src/core/view/ViewInterface.php';

class View implements ViewInterface
{
    public function __construct(
        private array $content = []
    )
    {}

    public function getContent(): array
    {
        return $this->content;
    }

    public function appendContent(ViewInterface $view): void
    {
        $contentAppending = $view->getContent();

        foreach ($contentAppending as $contentLine) {
            $this->content[] = $contentLine;
        }
    }
}
<?php
namespace App\Services;

include_once './src/core/ServiceInterface.php';
include_once './src/core/view/ViewInterface.php';

use App\Core\ServiceInterface;
use App\Core\ViewInterface;

class Window implements ServiceInterface
{
    private ?ViewInterface $view = null;

    public function refresh(ViewInterface $view = null): void
    {
        $this->clear();

        if (isset($view)) {
            $this->view = $view;
        }

        if (isset($this->view)) {
            $this->printView($this->view);
        }
    }

    public function appendViewPart(ViewInterface $view): void
    {
        isset($this->view) ? $this->view->appendContent($view) : $this->view = $view;
    }

    public function printView(ViewInterface $view): void
    {
        foreach ($view->getContent() as $line) {
            $this->printLine($line);
        }

        $this->printLine();
    }

    public function printLine(string $line = null): void
    {
        print "{$line}\n";
    }

    public function clear(): void
    {
        print "\x1B[H\x1B[J";
    }
}

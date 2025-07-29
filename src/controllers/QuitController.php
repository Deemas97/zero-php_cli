<?php
namespace App\Controllers;

include_once './src/core/controller/Response.php';
include_once './src/core/controller/ControllerInterface.php';
include_once './src/core/controller/ControllerResponseInterface.php';
include_once './src/templates/pages/QuitPage.php';

use App\Core\Controller\ControllerResponseInterface;
use App\Core\Events;
use App\Services\TemplateBuilder;
use App\Services\TemplateBuilder\ControllerRendered;
use App\Services\Window;
use App\Templates\QuitPage;

class QuitController extends ControllerRendered
{
    public function __construct(
        Window $window,
        TemplateBuilder $templateBuilder
    )
    {
        parent::__construct($window, $templateBuilder);
    }

    public function quit(): ControllerResponseInterface
    {
        $events = new Events();
        $events->addItem('quit', true);

        return $this->render(QuitPage::class, [
            'events' => $events
        ]);
    }
}

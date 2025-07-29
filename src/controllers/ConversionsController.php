<?php
namespace App\Controllers;

include_once './src/core/controller/Response.php';
include_once './src/core/controller/ControllerInterface.php';
include_once './src/core/controller/ControllerResponseInterface.php';
include_once './src/services/template_builder/ControllerRendered.php';
include_once './src/templates/pages/Conversions.php';

use App\Core\Controller\ControllerResponseInterface;
use App\Services\TemplateBuilder;
use App\Services\TemplateBuilder\ControllerRendered;
use App\Services\Window;
use App\Templates\Conversions;

class ConversionsController extends ControllerRendered
{
    public function __construct(
        Window $window,
        protected TemplateBuilder $templateBuilder
    )
    {
        parent::__construct($window, $templateBuilder);
    }

    public function index(): ControllerResponseInterface
    {
        return $this->render(Conversions::class, []);
    }
}

<?php
namespace App\Services\TemplateBuilder;

include_once './src/core/events/Events.php';
include_once './src/core/events/EventsInterface.php';
include_once './src/core/controller/ControllerBase.php';
include_once './src/core/controller/ControllerResponseInterface.php';
include_once './src/core/controller/Response.php';
include_once './src/core/view/ViewInterface.php';
include_once './src/services/Window.php';
include_once './src/services/TemplateBuilder.php';
include_once './src/services/template_builder/TemplateInterface.php';

use App\Core\Controller\ControllerResponseInterface;
use App\Core\ControllerBase;
use App\Core\Events;
use App\Core\EventsInterface;
use App\Services\TemplateBuilder;
use App\Services\Window;

abstract class ControllerRendered extends ControllerBase
{
    public function __construct(
        Window $window,
        protected TemplateBuilder $templateBuilder
    )
    {
        parent::__construct($window);
    }

    protected function getTemplate(string $templateName): TemplateInterface
    {
        return $this->templateBuilder->getTemplate(static::class, $templateName);
    }

    protected function render(string $templateName, array $data = []): ControllerResponseInterface
    {
        $template = $this->getTemplate($templateName);

        // Extract Events from Concrete ControllerRendered Data
        if (isset($data['events']) && ($data['events'] instanceof EventsInterface)) {
            $events = $data['events'];
        } else {
            $events = new Events();
        }
        ////

        // Bind parameters
        $parameters = [];
        if (is_array($eventsParameters = $events->get('parameters'))) {
            foreach ($eventsParameters as $name => $value) {
                $parameters[$name] = $value;
            }
        }
        $parameters['clInterfaceSpecification'] = $template->getInterfaceSpecification();
        $events->addItem('parameters', $parameters);
        ////

        $view = $template->getView(/*$data*/);
        $this->window->refresh($view);

        return $this->initResponse($events);
    }
}

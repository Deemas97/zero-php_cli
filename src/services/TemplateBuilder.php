<?php
namespace App\Services;

include_once './src/core/ServiceInterface.php';
include_once './src/services/template_builder/TemplateContainer.php';
include_once './src/services/template_builder/TemplateInterface.php';

use App\Core\ServiceInterface;
use App\Services\TemplateBuilder\TemplateContainer;
use App\Services\TemplateBuilder\TemplateInterface;

class TemplateBuilder implements ServiceInterface
{
    public function __construct(
        protected TemplateContainer $templates
    )
    {}

    public function getTemplate(string $id, string $templateName): ?TemplateInterface
    {
        if (!$this->templates->has($id)) {
            $this->templates->set($id, $templateName);
        }

        return $this->templates->get($id) ?? null;
    }
}
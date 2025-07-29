<?php
namespace App\Core;

include_once './src/core/controller/ControllerBase.php';
include_once './src/core/controller/ControllerResponseInterface.php';
include_once './src/services/InterfaceHandler.php';

use App\Core\Controller\ControllerResponseInterface;
use App\Interfaces\CommandLine\CommandLineSpecificationInterface;
use App\Services\InterfaceHandler;
use App\Services\Window;

class ControllerCLI extends ControllerBase
{
    public function __construct(
        Window $window,
        protected StoreTempProxy $temp,
        protected InterfaceHandler $interfaceHandler
    )
    {
        parent::__construct($window);
    }

    public function listen(CommandLineSpecificationInterface $clInterfaceSpecification = null): ControllerResponseInterface
    {
        $clInterfaceSpecificationTemp = null;

        if (isset($clInterfaceSpecification) && !empty($clInterfaceSpecification->getCommands()->getAll())) {
            $clInterfaceSpecificationTemp = $clInterfaceSpecification;
            $this->temp->addItem('clInterfaceSpecification', $clInterfaceSpecificationTemp);
        }

        $events = $this->interfaceHandler->process($clInterfaceSpecificationTemp ?? $this->temp->get('clInterfaceSpecification'));

        return $this->initResponse($events);
    }
}

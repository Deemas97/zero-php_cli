<?php
namespace App\Core;

include_once './src/core/ModuleInterface.php';
include_once './src/core/modules/ResponseManagerInterface.php';
include_once './src/core/request/Request.php';

class ResponseManager implements ModuleInterface, ResponseManagerInterface
{
    public function process(MessageBusInterface $messageBus): RequestInterface
    {
        $request = new Request();

        $responseDump = $messageBus->getAll();

        // if quit
        if (isset($responseDump['quit']) && $responseDump['quit'] === true) {
            $request->addItem('quit', true);
            return $request;
        }

        $request->addItem('route', $responseDump['route'] ?? ':cli');

        // get parameters
        $parameters = [];
        if (isset($responseDump['parameters'])) {
            $parameters['controller_method'] = $responseDump['parameters'];
        }

        $request->addItem('parameters', $parameters);

        return $request;
    }
}

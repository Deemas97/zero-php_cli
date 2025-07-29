<?php
namespace App\Controllers;

include_once './src/core/controller/ControllerBase.php';
include_once './src/core/controller/Response.php';
include_once './src/core/controller/ControllerResponseInterface.php';
include_once './src/services/api_connector/AffiseConnector.php';
include_once './src/services/template_builder/ControllerRendered.php';

use App\Core\Controller\ControllerResponseInterface;
use App\Core\ControllerBase;
use App\Services\AffiseConnector;
use App\Services\Window;

class OffersFixSmartlinkOffersController extends ControllerBase
{
    const  OFFERS_LIMIT = 500;
    const SMARTLINK_TAG = '[SMARTLINK]';
    const SMARTLINK_CURRENCY_DEFAULT = 'RUB';

    public function __construct(
        Window $window,
        protected AffiseConnector $affiseConnector
    )
    {
        parent::__construct($window);
    }

    public function index(
        int $id
    ): ControllerResponseInterface
    {
        $this->window->printLine("\n[Start]: Fix Offers descriptions\n");

        $this->window->printLine("\n[Processing]: Fix Offers descriptions\n");

        $this->window->printLine("\n[End]: Fix Offers descriptions\n");

        return $this->initResponse();
    }
}

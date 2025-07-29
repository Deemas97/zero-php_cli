<?php
namespace App\Controllers;

include_once './src/core/controller/ControllerBase.php';
include_once './src/core/controller/Response.php';
include_once './src/core/controller/ControllerInterface.php';
include_once './src/core/controller/ControllerResponseInterface.php';
include_once './src/services/api_connector/AffiseConnector.php';
include_once './src/services/template_builder/ControllerRendered.php';

use App\Core\Controller\ControllerResponseInterface;
use App\Core\ControllerBase;
use App\Services\AffiseConnector;
use App\Services\Window;

class ConversionsConvertToUsdController extends ControllerBase
{
    const FILE_OUTPUT_DIR = './public/output/';

    public function __construct(
        Window $window,
        protected AffiseConnector $affiseConnector
    )
    {
        parent::__construct($window);
    }

    public function index(
        string $date_from,
        string $date_to,
        float $currency,
        bool $save = false
    ): ControllerResponseInterface
    {
        $this->window->printLine("\n[Start]: Convert to USD\n");

        if ($save) {
            $savePath = self::FILE_OUTPUT_DIR . 'convert-conversions_dump_' . date('Y-m-d_H-i-s') . '.csv';
            $saveFile = fopen($savePath, 'wt');

            if ($saveFile !== false) {
                $saveFileHeader = [
                    'id',
                    'afprice',
                    'revenue_old',
                    'payouts_old',
                    'revenue_new',
                    'payouts_new'
                ];

                fputcsv($saveFile, $saveFileHeader, ',');
            }
        }

        $pagesCount = 1;

        $query = [
            'limit' => 500,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'currency' => 3
        ];

        for ($pageCurrent = 1; $pageCurrent <= $pagesCount; $pageCurrent++) {
            $conversions = [];

            $responseConversions = $this->affiseConnector->getConversions($query, $pageCurrent);

            if ($responseConversions->getStatus() === 0 || empty($responseConversions->getData()['conversions'])) {
                if (isset($saveFile)) {
                    fputcsv($saveFile, ['page: ' . $pageCurrent, 'Not found']);
                }
                $this->window->printLine("[Error]: Couldn't download conversions from page #$pageCurrent");
                break;
            }

            $pagination = $responseConversions->getPagination();
            if ($pagesCount === 1 && $pagination->getNextPage()) {
                $pagesCount = round($pagination->getTotalCount() / $pagination->getPerPage());

                // ...
            }

            foreach ($responseConversions->getData()['conversions'] as $conversion) {
                $rate = $conversion['revenue'] . '/' .  $conversion['payouts'];

                if (!array_key_exists($rate, $conversions)) {
                    $conversions[$rate] = [];
                }

                $conversions[$rate][] = $conversion['conversion_id'];
            }

            if (!empty($conversions)) {
                foreach ($conversions as $rateGroup => $conversionsIds) {
                    $rateItems = explode('/', $rateGroup);
                    $revenue = $rateItems[0];
                    $payouts = $rateItems[1];

//                    printf("old: %f\t%f\n", $revenue, $payouts);
//
//                    printf("new: %f\t%f\n\n", round($revenue * $currency, ((0.85 < 0.1) ? 3 : 2))
//                        , round($payouts * $currency, (($payouts < 0.1) ? 3 : 2)));

                    $dataEdit = [
                        'currency' => 'usd',
                        'revenue' => round($revenue * $currency, (($revenue < 0.1) ? 3 : 2)),
                        'payouts' => round($payouts * $currency, (($payouts < 0.1) ? 3 : 2)),
                    ];

                    foreach ($conversionsIds as $conversionId) {
                        $dataEdit['ids'][] = $conversionId;
                    }

                    $responseConversionsEdit = $this->affiseConnector->editConversions($dataEdit);

                    if ($responseConversionsEdit->getStatus() === 0) {
                        $this->window->printLine("[Error]: Couldn't update conversions from page #$pageCurrent");
                        break 2;
                    } else {
                        if (isset($saveFile)) {
                            foreach ($responseConversionsEdit->getData()['data']['ids'] as $conversionId) {
                                fputcsv($saveFile, [$conversionId, 'Converted'], ',');
                            }
                        }
                    }
                }
            } else {
                $this->window->printLine("[Warning]: Nothing to update on page #$pageCurrent");
            }
        }

        $this->window->printLine("\n[End]: Convert to USD\n");

        return $this->initResponse();
    }
}

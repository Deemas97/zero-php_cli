<?php
namespace App\Controllers;

include_once './src/core/controller/ControllerBase.php';
include_once './src/core/controller/ControllerInterface.php';
include_once './src/core/controller/ControllerResponseInterface.php';
include_once './src/services/template_builder/ControllerRendered.php';
include_once './src/services/api_connector/AffiseConnector.php';

use App\Core\Controller\ControllerResponseInterface;
use App\Core\ControllerBase;
use App\Services\AffiseConnector;
use App\Services\Window;

class ConversionsFixController extends ControllerBase
{
    const SEARCH_MASK_CLICKIDS = '/([0-9a-f]{24})/m';

    const AFFILIATES_SMARTLINKS = [
        11972
    ];

    const OFFERS_SMARTLINKS = [
        2560,
        2606
    ];

    const FILE_INPUT_DIR = './public/input/';
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
        bool $search_smartlinks = false,
        bool $save = false
    ): ControllerResponseInterface
    {
        $this->window->printLine("\n[Start]: Conversions fix\n");

        if ($save) {
            $savePath = self::FILE_OUTPUT_DIR . 'fix-conversions_dump_' . date('Y-m-d_H-i-s') . '.csv';
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

                fputcsv($saveFile, $saveFileHeader);
            }
        }

        $clickIds = $this->searchConversions(self::SEARCH_MASK_CLICKIDS);

        $query = [
            'date_from' => $date_from,
            'date_to' => $date_to
        ];

        foreach ($clickIds as $clickId) {
            $conversions['data'] = [];

            $query['clickid'] = $clickId;

            $responseConversions = $this->affiseConnector->getConversions($query);

            if ($responseConversions->getData() === null) {
                $this->window->printLine("[Error]: Connection error");
                $this->window->printLine("[Error]: Couldn't get conversion {'clickid' = '$clickId'}");
                $this->window->printLine("\n[End]: Conversions fix\n");
                return $this->initResponse();
            } elseif ($responseConversions->getStatus() === 0 || empty($responseConversions->getData()['conversions'])) {
                if (isset($saveFile)) {
                    fputcsv($saveFile, [$clickId, 'Not found']);
                }
                $this->window->printLine("[Warning]: ClickId '$clickId' is not found");
                continue;
            }

            foreach ($responseConversions->getData()['conversions'] as $conversion) {
                if (empty($conversion)) {
                    if (isset($saveFile)) {
                        fputcsv($saveFile, [$clickId, 'Not found']);
                    }
                    continue;
                }

                $statusClosed = false;
                $statusClosedReason = '';

                if ($conversion['payment_status'] === 'closed') {
                    $statusClosed = true;
                    $statusClosedReason = 'Payment status: Closed';
                } elseif ($conversion['status'] === 'declined') {
                    $statusClosed = true;
                    $statusClosedReason = 'Conversion status: Declined';
                }
                if ($statusClosed) {
                    if (isset($saveFile)) {
                        fputcsv($saveFile, [$clickId, $statusClosedReason]);
                    }
                    $this->window->printLine("[Warning]: ClickId '$clickId': $statusClosedReason");
                    continue 2;
                }

                $brokenRate = false;
                $brokenRateReason = '';

                if ($conversion['revenue'] <= 0 || $conversion['payouts'] <= 0) {
                    $brokenRate = true;
                    $brokenRateReason = 'Negative OR Zero numbers; ';
                } elseif ($conversion['revenue'] < $conversion['payouts']) {
                    $brokenRate = true;
                    $brokenRateReason = '([revenue] < [payouts]); ';
                }
                if ($conversion['sum'] <= 0) {
                    $brokenRate = true;
                    $brokenRateReason .= 'Empty afprice';
                }
                if ($brokenRate) {
                    $brokenRateReason = trim($brokenRateReason, '; ');
                    if (isset($saveFile)) {
                        fputcsv($saveFile, [$clickId, $brokenRateReason]);
                    }
                    $this->window->printLine("[Warning]: ClickId '$clickId': $brokenRateReason");
                    continue 2;
                }

                if ($conversion['sum'] !== $conversion['revenue']) {
                    $conversions['data'][] = [
                        'id' => $conversion['id'],
                        'afprice' => $conversion['sum'],
                        'revenue' => $conversion['revenue'],
                        'payouts' => $conversion['payouts']
                    ];

                    if ($search_smartlinks) {
                        $queryParametersSmartlink = [
                            'date_from' =>  $date_from,
                            'date_to' => $date_to
                        ];

                        if (in_array($conversion['offer_id'], self::OFFERS_SMARTLINKS)) {
                            $queryParametersSmartlink['action_id'] = trim($conversion['action_id'], 'sm');
                        } elseif (in_array($conversion['partner']['id'], self::AFFILIATES_SMARTLINKS)) {
                            $queryParametersSmartlink['action_id'] = 'sm' . $conversion['action_id'];
                        } else {
                            continue;
                        }

                        $conversionsSmartlink = $this->affiseConnector->getConversions($queryParametersSmartlink);

                        if ($conversionsSmartlink->getStatus() === 0) {
                            if (isset($saveFile)) {
                                fputcsv($saveFile, [$queryParametersSmartlink['action_id'], 'Not found']);
                            }
                            $this->window->printLine("[Warning]: Conversion with action_id {$queryParametersSmartlink['action_id']} isn't found");
                            continue;
                        }

                        foreach ($conversionsSmartlink->getData()['conversions'] as $conversionSmartlink) {
                            $conversions['data'][] = [
                                'id' => $conversionSmartlink['id'],
                                'afprice' => $conversion['sum'],
                                'revenue' => $conversionSmartlink['revenue'],
                                'payouts' => $conversionSmartlink['payouts']
                            ];

                            break 2;
                        }
                    }
                }
            }

            foreach ($conversions['data'] as $conversion) {
                $conversionUniqueId = $conversion['id'];

                $revenueNew = $conversion['afprice'];

                if ($conversion['revenue'] === $conversion['payouts']) {
                    $margeRatio = 1;
                } else {
                    $margeRatio = (float) bcdiv($conversion['payouts'], $conversion['revenue'], 3);
                }

                $payoutsNew = $conversion['afprice'] * $margeRatio;

                $dataEdit = [
                    'ids' => [$conversion['id']],
                    'revenue' => $revenueNew,
                    'payouts' => $payoutsNew
                ];

                $responseEdit = $this->affiseConnector->editConversions($dataEdit);

                if ($responseEdit->getStatus() === 0) {
                    $this->window->printLine("[Error]: Couldn't edit conversion {'id' = '$conversionUniqueId', 'clickid' = '$clickId'}");
                } else {
                    $this->window->printLine("[Success]: Conversion {'id' = '$conversionUniqueId', 'clickid' = '$clickId'} is edited");
                }

                if (isset($saveFile)) {
                    fputcsv($saveFile, $conversion);
                }
            }
        }

        $this->window->printLine("\n[End]: Conversions fix\n");

        return $this->initResponse();
    }

    private function searchConversions(string $mask): array
    {
        $fileInputPath = self::FILE_INPUT_DIR . 'regex.txt';
        $fileInputRows = file($fileInputPath, FILE_SKIP_EMPTY_LINES);

        if (!$fileInputRows) {
            return [];
        }

        $conversions = [];

        foreach ($fileInputRows as $inputRow) {
            $conversionStatus = preg_match($mask, $inputRow, $conversionAttributes);

            if ($conversionStatus !== false && !empty($conversionAttributes)) {
                foreach ($conversionAttributes as $conversionAttribute) {
                    $conversions[] = $conversionAttribute;
                }
            }
        }

        return $conversions;
    }
}

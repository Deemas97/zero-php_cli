<?php
namespace App\Src\Services\ApiConnector;

include_once './src/services/api_connector/responses/ApiResponse.php';
//include_once './src/services/api_connector/responses/affise_api_response/AffiliatesList.php';
//include_once './src/services/api_connector/responses/affise_api_response/ConversionsList.php';
include_once './src/services/api_connector/responses/affise_api_response/Pagination.php';

use App\Src\Services\ApiConnector\AffiseApiResponse\AffiliatesList;
use App\Src\Services\ApiConnector\AffiseApiResponse\ConversionsList;
use App\Src\Services\ApiConnector\AffiseApiResponse\Pagination;

class AffiseApiResponse extends ApiResponse
{
    const RESPONSE_SCHEMA = [
        'status',
        'pagination',
        'statusCode'
    ];

    protected ?array $pagination = null;
    protected ?int $statusCode = null;

    final public function getPagination(): Pagination
    {
        return new Pagination($this->pagination);
    }

    final public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }
}
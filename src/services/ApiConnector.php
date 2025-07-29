<?php
namespace App\Services;

include_once './src/core/ServiceInterface.php';

use App\Core\ServiceInterface;
use CURLFile;

abstract class ApiConnector implements ServiceInterface
{
    const CONNECTION_DELAY_SECONDS = 1;
    const CONNECTION_TIMEOUT_SECONDS = 100;
    const RETRYING_CONNECTION_COUNT = 5;

    const HTTP_HEADER = [
        'Accept-Language: ru',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'
    ];

    public function __construct(){}

    public function request(string $url, $postData = null): ?array
    {
        $this->setTimeout();

        $connection = curl_init();

        $options = $this->prepareOptions($url);

        if ($postData !== null) {
            $this->setPostMethod($options, $postData);
        }

        curl_setopt_array($connection, $options);

        $response = curl_exec($connection);
        $connectionErrorCode = curl_errno($connection);

        if ($connectionErrorCode !== 0) {
            print '[Error]: [CURL-connection]: #' . $connectionErrorCode . ' ' . curl_error($connection) . "\n";
        }

        curl_close($connection);

        return $this->decodeApiResponse($response);
    }

    public function requestWithRetrying(string $url, $postData = null): array
    {
        for ($i = 0; $i < static::RETRYING_CONNECTION_COUNT; $i++) {
            $response = $this->request($url, $postData);

            if (is_array($response)) {
                return $response;
            }
        }

        return [];
    }

    private function decodeApiResponse($response): ?array
    {
        if (isset($response) && is_string($response)) {
            $responseDecoded = json_decode($response, true);

            if (is_array($responseDecoded)) {
                return $responseDecoded;
            }
        }

        return null;
    }

    private function prepareOptions(string $url): array
    {
        return [
            CURLOPT_URL => $url,
            CURLOPT_CONNECTTIMEOUT => static::CONNECTION_TIMEOUT_SECONDS,
            CURLOPT_HEADER => false,
            CURLOPT_FAILONERROR => true,
            CURLOPT_HTTPHEADER => static::HTTP_HEADER,
            CURLOPT_RETURNTRANSFER => true
        ];
    }

    private function setPostMethod(array &$optionsConnection, $data = null): void
    {
        if (isset($data)) {
            $optionsConnection[CURLOPT_POST] = true;

            $this->setSimplePostData($optionsConnection, $data);

            if (is_array($data)) {
                $this->setArrayPostData($optionsConnection, $data);
            } elseif (is_string($data) && is_file($data)) {
                $this->setFilePostData($optionsConnection, $data);
            }
        }
    }

    private function setSimplePostData(array &$optionsConnection, $data): void
    {
        if ($data === 'POST') {
            $optionsConnection[CURLOPT_POSTFIELDS] = null;
        }
    }

    private function setFilePostData(array &$optionsConnection, $filePath): void
    {
        $fileName = strstr(basename($filePath), '.', true);

        $optionsConnection[CURLOPT_POSTFIELDS] = [
            $fileName => new CURLFile($filePath)
        ];

        clearstatcache(true, $filePath);
    }

    private function setArrayPostData(array &$optionsConnection, $data): void
    {
        if (is_array($data)) {
            $optionsConnection[CURLOPT_POSTFIELDS] = http_build_query($data);
        }
    }

    private function setTimeout(): void
    {
        sleep(static::CONNECTION_DELAY_SECONDS);
    }

    abstract protected function prepareUrl(string $method, array $query = null);
}

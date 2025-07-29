<?php
namespace App\Src\Services\ApiConnector;

abstract class ApiResponse
{
    const RESPONSE_SCHEMA = [
        'status',
    ];

    private ?int $status = null;
    private ?array $data = null;

    final public function __construct(array $responseArray = [])
    {
        foreach ($responseArray as $partName => $partData) {
            if (in_array($partName, static::RESPONSE_SCHEMA)) {
                $this->$partName = $partData;
            } else {
                $this->data[$partName] = $partData;
            }
        }
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function getData(): ?array
    {
        return $this->data;
    }
}

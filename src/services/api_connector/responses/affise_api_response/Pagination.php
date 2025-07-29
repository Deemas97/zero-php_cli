<?php
namespace App\Src\Services\ApiConnector\AffiseApiResponse;

class Pagination
{
    private ?int $perPage = null;
    private ?int $totalCount = null;
    private ?int $page = null;
    private ?int $nextPage = null;

    public function __construct(array $data)
    {
        $this->perPage = $data['per_page'] ?? null;
        $this->totalCount = $data['total_count'] ?? null;
        $this->page = $data['page'] ?? null;
        $this->nextPage = $data['next_page'] ?? null;
    }

    public function getPerPage(): ?int
    {
        return $this->perPage;
    }

    public function getTotalCount(): ?int
    {
        return $this->totalCount;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getNextPage(): ?int
    {
        return $this->nextPage;
    }
}

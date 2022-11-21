<?php

namespace App\Service;

use App\Repository\SearchRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class ListSearchService
{
    public function __construct(
        private RequestStack $requestStack,
        private SearchRepository $searchRepository,
    ) {

    }

    public function findAll(): array
    {
        $request = $this->requestStack->getMainRequest();
        $searchList = $this->searchRepository->findAll();

        return $searchList;
    }

}
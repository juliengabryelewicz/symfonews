<?php

namespace App\Service;

use App\Repository\SearchRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SearchService
{
    public function __construct(
        private RequestStack $requestStack,
        private SearchRepository $searchRepository,
        private PaginatorInterface $paginator
    ) {

    }

    public function getPaginatedSearches(): PaginationInterface
    {
        $request = $this->requestStack->getMainRequest();
        $searchesQuery = $this->searchRepository->findAll();
        $page = $request->query->getInt('page', 1);

        return $this->paginator->paginate($searchesQuery, $page, 10);
    }

}
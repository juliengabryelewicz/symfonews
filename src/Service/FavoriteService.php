<?php

namespace App\Service;

use App\Repository\FavoriteRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class FavoriteService
{
    public function __construct(
        private RequestStack $requestStack,
        private FavoriteRepository $favoriteRepository,
        private PaginatorInterface $paginator
    ) {

    }

    public function getPaginatedFavorites(): PaginationInterface
    {
        $request = $this->requestStack->getMainRequest();
        $favoriteQuery = $this->favoriteRepository->findAll();
        $page = $request->query->getInt('page', 1);

        return $this->paginator->paginate($favoriteQuery, $page, 10);
    }
}
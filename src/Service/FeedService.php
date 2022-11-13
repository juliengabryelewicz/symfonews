<?php

namespace App\Service;

use App\Repository\FeedRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class FeedService
{
    public function __construct(
        private RequestStack $requestStack,
        private FeedRepository $feedRepository,
        private PaginatorInterface $paginator
    ) {

    }

    public function getPaginatedFeeds(): PaginationInterface
    {
        $request = $this->requestStack->getMainRequest();
        $feedsQuery = $this->feedRepository->findAll();
        $page = $request->query->getInt('page', 1);

        return $this->paginator->paginate($feedsQuery, $page, 10);
    }

    public function isRssValid(string $link): bool
    {
        try{

            $link_content = simplexml_load_file($link);

            return isset($link_content->channel);

        }catch(\ErrorException $e){
            return false;
        }

    }
}
<?php

namespace App\Service;

use App\Entity\News;
use App\Entity\Feed;
use App\Repository\NewsRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class NewsService
{
    public function __construct(
        private RequestStack $requestStack,
        private NewsRepository $newsRepository,
        private PaginatorInterface $paginator
    ) {

    }

    public function getPaginatedNews(string $q = ""): PaginationInterface
    {
        $request = $this->requestStack->getMainRequest();
        $newsQuery = $this->newsRepository->getAllNews($q);
        $page = $request->query->getInt('page', 1);

        return $this->paginator->paginate($newsQuery, $page, 10);
    }


    public function convertXmlIntoNews(\SimpleXMLElement $news_content, Feed $feed): News
    {

            $news = new News();

            $news->setTitle($news_content->title)
            ->setDescription($news_content->description)
            ->setLink($news_content->link)
            ->setGuid($news_content->guid)
            ->setDate(new \DateTime($news_content->pubDate))
            ->setFeed($feed);

            return $news;
    }

}
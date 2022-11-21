<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\NewsService;
use App\Repository\FavoriteRepository;
use App\Repository\SearchRepository;

class HomeController extends AbstractController
{

    #[Route('/', name: 'home')]
    public function index(NewsService $newsService, FavoriteRepository $favoriteRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'news' => $newsService->getPaginatedNews(),
            'favorites' => $favoriteRepository->getAllGuids()
        ]);
    }

    #[Route('/search', name: 'home_search')]
    public function search(Request $request, NewsService $newsService, FavoriteRepository $favoriteRepository, SearchRepository $searchRepository): Response
    {

        $query = "";

        if(!empty($request->query->get("q"))){

            $query = $request->query->get("q");

        } else if(!empty($request->query->get("search"))){

            $search = $searchRepository->find($request->query->get("search"));
            $query = $search->getSearchQuery();

        }

        return $this->render('home/index.html.twig', [
            'news' => $newsService->getPaginatedNews($query),
            'favorites' => $favoriteRepository->getAllGuids()
        ]);
    }
}

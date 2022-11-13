<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Entity\News;
use App\Form\FavoriteType;
use App\Repository\FavoriteRepository;
use App\Repository\NewsRepository;
use App\Service\FavoriteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/favorite')]
class FavoriteController extends AbstractController
{

    #[Route('/', name: 'app_favorite_index', methods: ['GET'])]
    public function index(FavoriteService $favoriteService): Response
    {
        return $this->render('favorite/index.html.twig', [
            'favorites' => $favoriteService->getPaginatedFavorites(),
        ]);
    }

    #[Route('/add/{id}', name: 'app_favorite_add', methods: ['GET'])]
    public function add(News $news, Request $request, FavoriteRepository $favoriteRepository): Response
    {

        $favorite = new Favorite();

        $favorite->setTitle($news->getTitle())
        ->setDescription($news->getDescription())
        ->setLink($news->getLink())
        ->setGuid($news->getGuid())
        ->setDate($news->getDate())
        ->setFeedName($news->getFeed()->getName());

        $favoriteRepository->save($favorite, true);

        $route = $request->headers->get('referer');

        $this->addFlash(
            'success',
            $favorite->getTitle().' has been added to your favorite list'
        );

        return $this->redirect($route);

    }

    #[Route('/{id}', name: 'app_favorite_delete', methods: ['POST'])]
    public function delete(Request $request, Favorite $favorite, FavoriteRepository $favoriteRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$favorite->getId(), $request->request->get('_token'))) {
            $favoriteRepository->remove($favorite, true);

            $this->addFlash(
                'success',
                $favorite->getTitle().' has been removed'
            );
        }

        $route = $request->headers->get('referer');

        return $this->redirect($route);
    }
}

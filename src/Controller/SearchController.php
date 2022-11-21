<?php

namespace App\Controller;

use App\Entity\Search;
use App\Form\SearchQueryType;
use App\Repository\SearchRepository;
use App\Service\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/searchlist')]
class SearchController extends AbstractController
{
    #[Route('/', name: 'app_search_index', methods: ['GET'])]
    public function index(SearchService $searchService): Response
    {
        return $this->render('search/index.html.twig', [
            'searches' => $searchService->getPaginatedSearches(),
        ]);
    }

    #[Route('/new', name: 'app_search_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SearchRepository $searchRepository, SearchService $searchService): Response
    {
        $search = new Search();
        $form = $this->createForm(SearchQueryType::class, $search);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $searchRepository->save($search, true);

            $this->addFlash(
                'success',
                $search->getName().' has been saved'
            );

            return $this->redirectToRoute('app_search_index', [], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('search/new.html.twig', [
            'search' => $search,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_search_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Search $search, SearchRepository $searchRepository, SearchService $searchService): Response
    {
        $form = $this->createForm(SearchQueryType::class, $search);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $searchRepository->save($search, true);

            $this->addFlash(
                'success',
                $search->getName().' has been updated'
            );

            return $this->redirectToRoute('app_search_index', [], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('search/edit.html.twig', [
            'search' => $search,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_search_delete', methods: ['POST'])]
    public function delete(Request $request, Search $search, SearchRepository $searchRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$search->getId(), $request->request->get('_token'))) {
            $searchRepository->remove($search, true);

            $this->addFlash(
                'success',
                $search->getName().' has been deleted'
            );
        } else {
            $this->addFlash(
                'warning',
                $search->getName().' has not been deleted due to an error'
            );
        }

        return $this->redirectToRoute('app_search_index', [], Response::HTTP_SEE_OTHER);
    }

}

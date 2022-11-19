<?php

namespace App\Controller;

use App\Entity\Feed;
use App\Form\FeedType;
use App\Repository\FeedRepository;
use App\Service\FeedService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/feed')]
class FeedController extends AbstractController
{
    #[Route('/', name: 'app_feed_index', methods: ['GET'])]
    public function index(FeedService $feedService): Response
    {
        return $this->render('feed/index.html.twig', [
            'feeds' => $feedService->getPaginatedFeeds(),
        ]);
    }

    #[Route('/new', name: 'app_feed_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FeedRepository $feedRepository, FeedService $feedService): Response
    {
        $feed = new Feed();
        $form = $this->createForm(FeedType::class, $feed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if(!$feedService->IsRssValid($form->get('link')->getData())){

                $this->addWarningRssMessage();
                
            } else {

                $feedRepository->save($feed, true);

                $this->addFlash(
                    'success',
                    $feed->getName().' has been saved'
                );
    
                return $this->redirectToRoute('app_feed_index', [], Response::HTTP_SEE_OTHER);

            }

        }

        return $this->renderForm('feed/new.html.twig', [
            'feed' => $feed,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_feed_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Feed $feed, FeedRepository $feedRepository, FeedService $feedService): Response
    {
        $form = $this->createForm(FeedType::class, $feed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if(!$feedService->IsRssValid($form->get('link')->getData())){

                $this->addWarningRssMessage();
                
            } else{

                $feedRepository->save($feed, true);

                $this->addFlash(
                    'success',
                    $feed->getName().' has been updated'
                );
    
                return $this->redirectToRoute('app_feed_index', [], Response::HTTP_SEE_OTHER);

            }
        }

        return $this->renderForm('feed/edit.html.twig', [
            'feed' => $feed,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_feed_delete', methods: ['POST'])]
    public function delete(Request $request, Feed $feed, FeedRepository $feedRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$feed->getId(), $request->request->get('_token'))) {
            $feedRepository->remove($feed, true);

            $this->addFlash(
                'success',
                $feed->getName().' has been deleted'
            );
        } else {
            $this->addFlash(
                'warning',
                $feed->getName().' has not been deleted due to an error'
            );
        }

        return $this->redirectToRoute('app_feed_index', [], Response::HTTP_SEE_OTHER);
    }

    private function addWarningRssMessage(): void
    {

        $this->addFlash(
            'warning',
            'The RSS Link is not correct'
        );

    }
}

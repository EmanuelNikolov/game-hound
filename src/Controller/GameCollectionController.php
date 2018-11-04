<?php

namespace App\Controller;

use App\Entity\GameCollection;
use App\Form\GameCollectionType;
use App\Repository\CollectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/game/collection")
 */
class GameCollectionController extends AbstractController
{

    /**
     * @Route("/", name="game_collection_index", methods="GET")
     */
    public function index(CollectionRepository $collectionRepository): Response
    {
        return $this->render('game_collection/index.html.twig', [
          'game_collections' => $collectionRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="game_collection_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $gameCollection = (new GameCollection())->setUser($this->getUser());
        $form = $this->createForm(GameCollectionType::class, $gameCollection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($gameCollection);
            $em->flush();

            return $this->redirectToRoute('game_collection_index');
        }

        return $this->render('game_collection/new.html.twig', [
          'game_collection' => $gameCollection,
          'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="game_collection_show", methods="GET")
     */
    public function show(GameCollection $gameCollection): Response
    {
        return $this->render('game_collection/show.html.twig',
          ['game_collection' => $gameCollection]);
    }

    /**
     * @Route("/{id}/edit", name="game_collection_edit", methods="GET|POST")
     */
    public function edit(
      Request $request,
      GameCollection $gameCollection
    ): Response {
        $form = $this->createForm(GameCollectionType::class, $gameCollection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('game_collection_edit',
              ['id' => $gameCollection->getId()]);
        }

        return $this->render('game_collection/edit.html.twig', [
          'game_collection' => $gameCollection,
          'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="game_collection_delete", methods="DELETE")
     */
    public function delete(
      Request $request,
      GameCollection $gameCollection
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $gameCollection->getId(),
          $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($gameCollection);
            $em->flush();
        }

        return $this->redirectToRoute('game_collection_index');
    }
}

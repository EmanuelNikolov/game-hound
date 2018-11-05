<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\GameCollection;
use App\FlashMessage\GameCollectionMessage as Flash;
use App\Form\GameCollectionType;
use App\Repository\CollectionRepository;
use App\Security\Voter\GameCollectionVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameCollectionController extends AbstractController
{

    /**
     * @Route("/collections", name="game_collection_index", methods="GET")
     *
     * @param CollectionRepository $collectionRepository
     *
     * @return Response
     */
    public function latest(CollectionRepository $collectionRepository): Response
    {
        return $this->render('game_collection/index.html.twig', [
          'game_collections' => $collectionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/collection/new", name="game_collection_new", methods="GET|POST")
     *
     * @param Request $request
     *
     * @return Response
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

            $this->addFlash('success', Flash::COLLECTION_CREATED);

            return $this->redirectToRoute('game_collection_index');
        }

        return $this->render('game_collection/new.html.twig', [
          'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/collection/{id}", name="game_collection_show", methods="GET")
     *
     * @param GameCollection $gameCollection
     *
     * @return Response
     */
    public function show(GameCollection $gameCollection): Response
    {
        return $this->render('game_collection/show.html.twig',
          ['game_collection' => $gameCollection]);
    }

    /**
     * @Route("/collection/{id}/edit", name="game_collection_edit",
     *   methods="GET|POST")
     *
     * @param Request $request
     * @param GameCollection $gameCollection
     *
     * @return Response
     */
    public function update(
      Request $request,
      GameCollection $gameCollection
    ): Response {
        $this->denyAccessUnlessGranted(
          GameCollectionVoter::EDIT,
          $gameCollection
        );

        $form = $this->createForm(GameCollectionType::class, $gameCollection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('game_collection_edit', [
              'id' => $gameCollection->getId(),
            ]);
        }

        return $this->render('game_collection/edit.html.twig', [
          'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/collection/{id}", name="game_collection_delete",
     *   methods="DELETE")
     *
     * @param Request $request
     * @param GameCollection $gameCollection
     *
     * @return RedirectResponse
     */
    public function delete(
      Request $request,
      GameCollection $gameCollection
    ): RedirectResponse {
        $this->denyAccessUnlessGranted(
          GameCollectionVoter::EDIT,
          $gameCollection
        );

        $validation = $this->isCsrfTokenValid(
          'delete' . $gameCollection->getId(),
          $request->request->get('_token')
        );

        if ($validation) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($gameCollection);
            $em->flush();
        }

        return $this->redirectToRoute('game_collection_index');
    }

    /**
     * @Route(
     *     "/collection/{id}/add/{game_id}",
     *     name="game_collection_add",
     *     methods="GET"
     * )
     * @ParamConverter("game", options={"id" = "game_id"})
     * @IsGranted("GAME_COLLECTION_EDIT", subject="collection")
     *
     * @param GameCollection $collection
     * @param Game $game
     */
    public function add(GameCollection $collection, Game $game)
    {
        // TODO
    }

    /**
     * @Route(
     *     "collection/{id}/remove/{game_id}",
     *     name="game_collection_remove",
     *     methods="DELETE"
     * )
     * @ParamConverter("game", options={"id" = "game_id"})
     * @IsGranted("GAME_COLLECTION_EDIT", subject="collection")
     *
     * @param GameCollection $collection
     * @param Game $game
     */
    public function remove(GameCollection $collection, Game $game)
    {
        // TODO
    }
}

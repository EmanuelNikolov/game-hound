<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\GameCollection;
use App\FlashMessage\GameCollectionMessage as Flash;
use App\Form\GameCollectionType;
use App\Repository\CollectionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @param GameCollection $collection
     *
     * @return Response
     */
    public function show(GameCollection $collection): Response
    {
        return $this->render('game_collection/show.html.twig', [
          'game_collection' => $collection
        ]);
    }

    /**
     * @Route(
     *     "/collection/{id}/edit",
     *     name="game_collection_edit",
     *     methods="GET|POST"
     * )
     * @IsGranted("GAME_COLLECTION_EDIT", subject="collection")
     *
     * @param Request $request
     * @param GameCollection $collection
     *
     * @return Response
     */
    public function update(
      Request $request,
      GameCollection $collection
    ): Response {
        $form = $this->createForm(GameCollectionType::class, $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('game_collection_edit', [
              'id' => $collection->getId(),
            ]);
        }

        return $this->render('game_collection/edit.html.twig', [
          'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(
     *     "/collection/{id}",
     *     name="game_collection_delete",
     *     methods="DELETE"
     * )
     * @IsGranted("GAME_COLLECTION_EDIT", subject="collection")
     *
     * @param Request $request
     * @param GameCollection $collection
     *
     * @return Response
     */
    public function delete(
      Request $request,
      GameCollection $collection
    ): Response {
        $validation = $this->isCsrfTokenValid(
          'delete' . $collection->getId(),
          $request->request->get('_token')
        );

        if ($validation) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($collection);
            $em->flush();
        }

        return $this->redirectToRoute('game_collection_index');
    }

    /**
     * @Route(
     *     "/collection/{id}/add/{game_id}",
     *     name="game_collection_add",
     *     methods="POST"
     * )
     * @ParamConverter("game", options={"id" = "game_id"})
     * @IsGranted("GAME_COLLECTION_EDIT", subject="collection")
     *
     * @param GameCollection $collection
     * @param Game $game
     *
     * @return Response
     */
    public function add(GameCollection $collection, Game $game): Response
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
     *
     * @return Response
     */
    public function remove(GameCollection $collection, Game $game): Response
    {
        // TODO
    }
}

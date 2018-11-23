<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\GameCollection;
use App\FlashMessage\GameCollectionMessage as Flash;
use App\Form\GameCollectionType;
use App\Repository\GameCollectionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameCollectionController extends AbstractController
{

    public const PAGE_LIMIT = 10;

    /**
     * @Route("/collections", name="game_collection_index", methods="GET")
     *
     * @param Request $request
     * @param GameCollectionRepository $repo
     *
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    public function index(
      Request $request,
      GameCollectionRepository $repo,
      PaginatorInterface $paginator
    ): Response {
        $pagination = $paginator->paginate(
          $repo->createQueryBuilder('gc'),
          $request->query->getInt('page', 1),
          self::PAGE_LIMIT
        );

        return $this->render('game_collection/index.html.twig', [
          'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/collection/new", name="game_collection_new", methods="GET|POST")
     * @IsGranted("ROLE_USER_CONFIRMED", message="You have to verify your email before you can do that.")
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

            return $this->redirectToRoute('game_collection_show', [
              'id' => $gameCollection->getId(),
            ]);
        }

        return $this->render('game_collection/new.html.twig', [
          'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/collection/{id}", name="game_collection_show", methods="GET")
     *
     * @param Request $request
     * @param GameCollection $collection
     *
     * @return Response
     */
    public function show(
      Request $request,
      GameCollection $collection
    ): Response {
        $offset = 0;
        $pageLimit = GameController::PAGE_LIMIT;

        if ($request->isXmlHttpRequest() && $request->query->has('offset')) {
            $offset = $request->query->get('offset') + $pageLimit;
            $games = $collection->getPaginatedGames($offset, $pageLimit);
            $gamesArr = [];

            foreach ($games as $game) {
                $gamesArr[] = [
                  'name' => $game->getName(),
                  'slug' => $game->getSlug(),
                  'cover' => ['cloudinary_id' => $game->getCover()],
                ];
            }

            return new JsonResponse($gamesArr);
        }

        return $this->render('game_collection/show.html.twig', [
          'game_collection' => $collection,
          'offset' => $offset,
          'page_limit' => $pageLimit,
        ]);
    }

    /**
     * @Route(
     *     "/collection/{id}/edit",
     *     name="game_collection_edit",
     *     methods="GET|POST"
     * )
     * @IsGranted("GAME_COLLECTION_EDIT", subject="collection")

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
     * @Security("is_granted('ROLE_USER_CONFIRMED') and is_granted('GAME_COLLECTION_EDIT', collection)")
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

            $this->addFlash('success', Flash::COLLECTION_DELETED);
        }

        return $this->redirectToRoute('user_show', [
          'username' => $collection->getUser()->getUsername(),
        ]);
    }

    /**
     * @Route(
     *     "/collection/{id}/add/{game_id}",
     *     name="game_collection_add_game",
     *     methods="PUT"
     * )
     * @ParamConverter("game", options={"id" = "game_id"})
     * @Security("is_granted('ROLE_USER_CONFIRMED') and is_granted('GAME_COLLECTION_EDIT', collection)")
     *
     * @param GameCollection $collection
     * @param Game $game
     *
     * @return Response
     */
    public function addGame(
      GameCollection $collection,
      Game $game
    ): Response {
        $collection->addGame($game);

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('game_collection_show', [
          'id' => $collection->getId(),
        ]);
    }

    /**
     * @Route(
     *     "/collection/{id}/remove/{game_id}",
     *     name="game_collection_remove_game",
     *     methods="DELETE"
     * )
     * @ParamConverter("game", options={"id" = "game_id"})
     * @Security("is_granted('ROLE_USER_CONFIRMED') and is_granted('GAME_COLLECTION_EDIT', collection)")
     *
     * @param Request $request
     * @param GameCollection $collection
     * @param Game $game
     *
     * @return Response
     */
    public function removeGame(
      Request $request,
      GameCollection $collection,
      Game $game
    ): Response {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(null, 403);
        }

        $collection->removeGame($game);

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(null, 200);
    }
}

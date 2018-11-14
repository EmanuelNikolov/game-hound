<?php

namespace App\Controller;


use App\Entity\Game;
use Doctrine\ORM\EntityManagerInterface;
use EN\IgdbApiBundle\Igdb\IgdbWrapperInterface;
use EN\IgdbApiBundle\Igdb\Parameter\ParameterBuilderInterface;
use EN\IgdbApiBundle\Igdb\ValidEndpoints;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class GameController extends AbstractController
{

    /**
     * @var IgdbWrapperInterface
     */
    private $wrapper;

    /**
     * @var ParameterBuilderInterface
     */
    private $builder;

    /**
     * @var DenormalizerInterface
     */
    private $denormalizer;

    /**
     * GameController constructor.
     *
     * @param IgdbWrapperInterface $wrapper
     * @param ParameterBuilderInterface $builder
     * @param DenormalizerInterface $denormalizer
     */
    public function __construct(
      IgdbWrapperInterface $wrapper,
      ParameterBuilderInterface $builder,
      DenormalizerInterface $denormalizer
    ) {
        $this->wrapper = $wrapper;
        $this->builder = $builder;
        $this->denormalizer = $denormalizer;
    }

    /**
     * @Route("/game/search/{name}", name="game_search", methods={"GET"})
     *
     * @param Request $request
     * @param string $name
     *
     * @return Response
     * @throws \EN\IgdbApiBundle\Exception\ScrollHeaderNotFoundException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function search(
      Request $request,
      string $name
    ): Response {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(null, 403);
        }

        $pageLimit = 8;

        $this->builder
          ->setSearch($name)
          ->setFields('name,slug,cover')
          ->setLimit($pageLimit);

        if ($request->query->has('offset')) {
            $pageOffset = $request->query->get('offset') + $pageLimit;
            $this->builder->setOffset((int)$pageOffset);

            $games = $this->wrapper->fetchDataAsJson(
              ValidEndpoints::GAMES,
              $this->builder
            );
        } else {
            $pageOffset = 0 + $pageLimit;
        }

        $games = $this->wrapper->fetchDataAsJson(
          ValidEndpoints::GAMES,
          $this->builder
        );

        $statusCode = $this->wrapper->getResponse()->getStatusCode();

        return JsonResponse::fromJsonString($games, $statusCode, [
          'Offset' => $pageOffset,
        ]);
    }

    /**
     * @Route("game/{slug}", name="game_show", methods={"GET"})
     *
     * @param string $slug
     * @param EntityManagerInterface $em
     *
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function show(string $slug, EntityManagerInterface $em): Response
    {
        $game = $em->getRepository(Game::class)->findOneBySlug($slug);

        if (!$game) {
            $this->builder
              ->setSearch($slug)
              ->setFields('name,slug,summary,first_release_date,cover')
              ->setLimit(1);

            $game = $this->denormalize($this->wrapper->games($this->builder))[0];

            $em->persist($game);
            $em->flush();
        }

        return $this->render('game/show.html.twig', ['game' => $game]);
    }

    /**
     * @Route("/test")
     * @throws \EN\IgdbApiBundle\Exception\ScrollHeaderNotFoundException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function test()
    {
        $this->builder->setLimit(30)->setSearch('mass')->setFields('name,cover');
        $games = $this->wrapper->fetchData(ValidEndpoints::GAMES,
          $this->builder);
        dd($games);
        $cache = new FilesystemCache();
        dd($cache->get('games.search'));
    }

    /**
     * @param array $games
     *
     * @return array
     */
    private function denormalize(array $games): array
    {
        $gamesNormalized = [];

        foreach ($games as $game) {
            $gamesNormalized[] = $this->denormalizer->denormalize(
              $game,
              Game::class,
              null,
              [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]
            );
        }

        return $gamesNormalized;
    }
}
<?php

namespace App\Controller;


use App\Entity\Game;
use Doctrine\ORM\EntityManagerInterface;
use EN\IgdbApiBundle\Igdb\IgdbWrapper;
use EN\IgdbApiBundle\Igdb\IgdbWrapperInterface;
use EN\IgdbApiBundle\Igdb\Parameter\ParameterBuilderInterface;
use EN\IgdbApiBundle\Igdb\ValidEndpoints;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route("/search/{name}", name="game_search", methods={"GET"})
     * @Route("/search/scroll", name="game_search_scroll", methods={"GET"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param string $name
     *
     * @return Response
     * @throws \EN\IgdbApiBundle\Exception\ScrollHeaderNotFoundException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function search(
      Request $request,
      SessionInterface $session,
      string $name
    ): Response {
        if ($request->isXmlHttpRequest()) {
            $scrollHeader = $session->get('scroll');
            $games = $this->wrapper->scroll($scrollHeader);

            return new JsonResponse($games);
        }

        $this->builder
          ->setSearch($name)
          ->setFields('name,slug,cover')
          ->setScroll(1);
        $games = $this->wrapper->games($this->builder);
        $gamesNormalized = $this->denormalize($games);

        $scrollHeader = $this->wrapper->getScrollHeader(
          $this->wrapper->getResponse(),
          IgdbWrapper::SCROLL_NEXT_PAGE
        );
        $session->set('scroll', $scrollHeader);

        return $this->render('game/search.html.twig', [
          'games' => $gamesNormalized,
          'query' => $name,
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

        return $this->render('game/show.html.twig', [
          'game' => $game,
        ]);
    }

    /**
     * @Route("test")
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function test()
    {
        $this->builder->setLimit(1)->setSearch('mass effect andromeda');
        $game = $this->wrapper->fetchData(ValidEndpoints::GAMES,
          $this->builder);
        dd($game);
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
            $gamesNormalized[] = $this->denormalizer->denormalize($game,
              Game::class);
        }

        return $gamesNormalized;
    }
}
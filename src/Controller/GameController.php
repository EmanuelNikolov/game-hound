<?php

namespace App\Controller;


use App\Entity\Game;
use EN\IgdbApiBundle\Igdb\IgdbWrapperInterface;
use EN\IgdbApiBundle\Igdb\Parameter\ParameterBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class GameController extends AbstractController
{

    /**
     * @Route("/games/search/{name}", name="game_search", methods={"GET"})
     *
     * @param string $name
     * @param IgdbWrapperInterface $wrapper
     * @param ParameterBuilderInterface $builder
     * @param DenormalizerInterface $denormalizer
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function search(
      string $name,
      IgdbWrapperInterface $wrapper,
      ParameterBuilderInterface $builder,
    DenormalizerInterface $denormalizer
    ) {
        $games = $wrapper->games($builder->setSearch($name)->setExpand('genres'));
        $gamesNormalized = [];

        foreach ($games as $game) {
            $gamesNormalized[] = $denormalizer->denormalize($game, Game::class);
        }

        dd($gamesNormalized);
    }
}
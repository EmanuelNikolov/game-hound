<?php

namespace App\Controller;


use EN\IgdbApiBundle\Igdb\IgdbWrapperInterface;
use EN\IgdbApiBundle\Igdb\Parameter\ParameterBuilderInterface;
use EN\IgdbApiBundle\Igdb\ValidEndpoints;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IgdbController extends AbstractController
{

    /**
     * @Route("test")
     * @param \EN\IgdbApiBundle\Igdb\IgdbWrapperInterface $wrapper
     * @param \EN\IgdbApiBundle\Igdb\Parameter\ParameterBuilderInterface $builder
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function test(
      IgdbWrapperInterface $wrapper,
      ParameterBuilderInterface $builder
    ) {
        dd($wrapper->search('Firewatch',
          ValidEndpoints::GAMES, $builder));
    }
}
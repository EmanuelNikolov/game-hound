<?php

namespace App\Controller;


use EN\IgdbApiBundle\Igdb\IgdbWrapperInterface;
use EN\IgdbApiBundle\Igdb\Parameter\ParameterBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IgdbController extends AbstractController
{

    /**
     * @Route("test")
     * @param \EN\IgdbApiBundle\Igdb\IgdbWrapperInterface $wrapper
     * @param \EN\IgdbApiBundle\Igdb\Parameter\ParameterBuilder $builder
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function test(IgdbWrapperInterface $wrapper, ParameterBuilder $builder)
    {
        dd($wrapper->games($builder->setId(1)));
    }
}
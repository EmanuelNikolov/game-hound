<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="home", methods={"GET"})
     *
     * @param \App\Repository\GameRepository $repo
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(GameRepository $repo): Response
    {
        return $this->render('home/index.html.twig', [
          'games' => $repo->findLatest(),
        ]);
    }
}

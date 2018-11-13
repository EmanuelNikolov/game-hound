<?php

namespace App\Controller;

use App\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function index(): Response
    {
        $games = $this->getDoctrine()->getRepository(Game::class)->findLatest();

        return $this->render('home/index.html.twig', [
          'games' => $games,
        ]);
    }
}

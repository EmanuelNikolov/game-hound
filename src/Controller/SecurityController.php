<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * @Route("/login", name="security_login", methods={"GET", "POST"})
     *
     * @param \Symfony\Component\Security\Http\Authentication\AuthenticationUtils $authenticationUtils
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
          'last_username' => $lastUsername,
          'error' => $error,
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     *
     * @throws \Exception
     */
    public function logout()
    {
        throw new \Exception("Shouldn't reach this!");
    }

    /**
     * @Route("/admin")
     */
    public function admin()
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
    }
}

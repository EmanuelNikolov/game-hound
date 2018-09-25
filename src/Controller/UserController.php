<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{

    /**
     * @Route("/register", name="user_register")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @param \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $userPasswordEncoder
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(
      Request $request,
      UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $user = new User();
        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPassword = $userPasswordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encodedPassword);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            die('ded');
        }

        return $this->render('user/signup.html.twig', [
          'form' => $form->createView(),
        ]);
    }
}

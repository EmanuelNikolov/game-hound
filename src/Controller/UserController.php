<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{

    private const REGISTER_SUCCESS_MESSAGE = 'Yay! Your account was created, a verification link was sent to your email :)';

    /**
     * @Route("/register", name="user_register", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param UsernamePasswordToken $usernamePasswordToken
     * @param TokenStorageInterface $tokenStorage
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    //TODO: Implement GuardHandler instead of old method for login after User creation or modification
    public function register(
      Request $request,
      UserPasswordEncoderInterface $userPasswordEncoder,
      UsernamePasswordToken $usernamePasswordToken,
      TokenStorageInterface $tokenStorage
    ) {
        $user = new User();
        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPassword = $userPasswordEncoder->encodePassword($user,
              $user->getPlainPassword());
            $user->setPassword($encodedPassword);

            // TODO: Make a migration
            $emailConfirmationToken = bin2hex(random_bytes(32));
            $user->setEmailConfirmationToken($emailConfirmationToken);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', self::REGISTER_SUCCESS_MESSAGE);

            $loginToken = new UsernamePasswordToken($user, $encodedPassword, 'main');
            $tokenStorage->setToken($loginToken);
            $request
              ->getSession()
              ->set('_security_main', serialize($loginToken));

            return $this->redirectToRoute('home');
        }

        return $this->render('user/signup.html.twig', [
          'form' => $form->createView(),
        ]);
    }

    /*public function emailConfirmation(string $token, Request $request, GuardH)
    {

    }*/
}

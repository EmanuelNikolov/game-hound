<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\UserRegisteredEvent;
use App\Form\UserRegisterType;
use App\Security\UserLoginAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class UserController extends AbstractController
{

    private const REGISTER_SUCCESS_MESSAGE = "Yay! Your account was created, a verification link was sent to your email :)";

    private const EMAIL_CONFIRM_FAIL = "Invalid token :(";

    private const EMAIL_CONFIRMED_SUCCESS = "Your email has been verified successfully! Enjoy Game Hound <3";

    private $guardAuthenticatorHandler;

    private $userLoginAuthenticator;

    /**
     * UserController constructor.
     *
     * @param GuardAuthenticatorHandler $guardAuthenticatorHandler
     * @param UserLoginAuthenticator $userLoginAuthenticator
     */
    public function __construct(
      GuardAuthenticatorHandler $guardAuthenticatorHandler,
      UserLoginAuthenticator $userLoginAuthenticator
    ) {
        $this->guardAuthenticatorHandler = $guardAuthenticatorHandler;
        $this->userLoginAuthenticator = $userLoginAuthenticator;
    }

    /**
     * @Route("/register", name="user_register", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(
      Request $request,
      UserPasswordEncoderInterface $userPasswordEncoder,
      EventDispatcherInterface $eventDispatcher,
    ParameterBagInterface $bag
    ) {
        dd($bag->set('mailer.user.templates', 'shkrr'));
        $user = new User();
        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPassword = $userPasswordEncoder->encodePassword($user,
              $user->getPlainPassword());
            $user->setPassword($encodedPassword);

            $event = new UserRegisteredEvent($user);
            $eventDispatcher->dispatch(UserRegisteredEvent::NAME, $event);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', self::REGISTER_SUCCESS_MESSAGE);

            return $this->guardAuthenticatorHandler
              ->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $this->userLoginAuthenticator,
                'main'
              );
        }

        return $this->render('user/signup.html.twig', [
          'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(
     *     "/account/confirm/{token}",
     *     name="user_email_confirm",
     *     methods={"GET"}
     * )
     *
     * @param string $token
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function emailConfirm(
      string $token,
      Request $request
    ) {
        $em = $this->getDoctrine()->getManager();
        /** @var \App\Entity\User $user */
        $user = $em->getRepository(User::class)
          ->findOneByEmailConfirmationToken($token);

        if (!$user) {
            $this->addFlash('danger', self::EMAIL_CONFIRM_FAIL);

            return $this->redirectToRoute('home');
        }

        $user->setEmailConfirmationToken(null);
        $em->flush();

        $this->addFlash('success', self::EMAIL_CONFIRMED_SUCCESS);

        return $this->guardAuthenticatorHandler
          ->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $this->userLoginAuthenticator,
            'main'
          );
    }
}

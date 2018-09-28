<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\UserRegisteredEvent;
use App\Form\UserRegisterType;
use App\Security\UserLoginAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class UserController extends AbstractController
{

    private const REGISTER_SUCCESS_MESSAGE = "Yay! Your account was created, a verification link was sent to your email (つ ♡ ͜ʖ ♡)つ";

    private const EMAIL_CONFIRM_INVALID_TOKEN = "Invalid token ¯\_( ͡° ͜ʖ ͡°)_/¯";

    private const EMAIL_CONFIRM_USER_DIFF = "You must be logged into your account to confirm your email ( ͡ಠ ʖ̯ ͡ಠ)";

    private const EMAIL_CONFIRM_SUCCESS = "Your email has been verified successfully! Enjoy Game Hound ( ͡°з ͡°)";

    private const EMAIL_CONFIRM_ALREADY_DONE = "You have already verified your email ¯\_(⊙_ʖ⊙)_/¯";

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
      EventDispatcherInterface $eventDispatcher
    ) {
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
          ->findOneByConfirmationToken($token);

        // TODO: put that logic in a separate service
        if (!$user) {
            if (!in_array(User::ROLE_USER_UNCONFIRMED, $user->getRoles())) {
                $this->addFlash('notice', self::EMAIL_CONFIRM_ALREADY_DONE);
            }
            $this->addFlash('danger', self::EMAIL_CONFIRM_INVALID_TOKEN);

            return $this->redirectToRoute('security_login');
        }

        if (!$user->isEqualTo($this->getUser())) {
            $this->addFlash('danger', self::EMAIL_CONFIRM_USER_DIFF);

            return $this->redirectToRoute('security_login');
        }

        $user->setConfirmationToken(null);
        $user->setRoles([User::ROLE_USER_CONFIRMED]);
        $em->flush();

        $this->addFlash('success', self::EMAIL_CONFIRM_SUCCESS);

        return $this->guardAuthenticatorHandler
          ->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $this->userLoginAuthenticator,
            'main'
          );
    }

    /**
     * @Route("/{username}", name="user_profile", methods={"GET"})
     *
     * @param \App\Entity\User $user
     */
    public function profile(User $user)
    {
        die('sex');
    }
}

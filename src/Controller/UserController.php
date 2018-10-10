<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\UserEvent;
use App\Form\UserNewPasswordType;
use App\Form\UserRegisterType;
use App\Form\UserResetPasswordType;
use App\Security\UserLoginAuthenticator;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class UserController extends AbstractController
{

    private const REGISTRATION_CONFIRMED = "Yay! Your account was created, a verification link was sent to your email (つ ♡ ͜ʖ ♡)つ";

    private const INVALID_TOKEN = "Invalid token ¯\_( ͡° ͜ʖ ͡°)_/¯";

    private const EMAIL_CONFIRM_USER_DIFF = "You must be logged into your account to confirm your email ( ͡ಠ ʖ̯ ͡ಠ)";

    private const REGISTRATION_SUCCESS = "Your email has been verified successfully! Enjoy Game Hound ( ͡°з ͡°)";

    private const RESET_PASSWORD_SUCCESS = "You have successfully changed your password. Welcome back! ( ͡°з ͡°)";

    private const RESET_PASSWORD_REQUEST_FAIL = "Sorry, we couldn't find a user account with those credentials ┐( ͡° ʖ̯ ͡°)┌";

    private const RESET_PASSWORD_REQUEST_SUCCESS = "Wooho! A password reset link was sent to your email! ヽ༼ຈل͜ຈ༽ﾉ";

    const RESET_PASSWORD_FAIL = "You have to verify your email before you can reset your password! ( ͡ಠ ʖ̯ ͡ಠ)";

    private $guardAuthenticatorHandler;

    private $userLoginAuthenticator;

    private $eventDispatcher;

    /**
     * UserController constructor.
     *
     * @param GuardAuthenticatorHandler $guardAuthenticatorHandler
     * @param UserLoginAuthenticator $userLoginAuthenticator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
      GuardAuthenticatorHandler $guardAuthenticatorHandler,
      UserLoginAuthenticator $userLoginAuthenticator,
      EventDispatcherInterface $eventDispatcher
    ) {
        $this->guardAuthenticatorHandler = $guardAuthenticatorHandler;
        $this->userLoginAuthenticator = $userLoginAuthenticator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/register", name="user_register", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $userPasswordEncoder
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
            $encodedPassword = $userPasswordEncoder->encodePassword($user,
              $user->getPlainPassword());
            $user->setPassword($encodedPassword);

            $event = new UserEvent($user);
            $this->eventDispatcher->dispatch(UserEvent::REGISTER_REQUEST,
              $event);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', self::REGISTRATION_CONFIRMED);

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
     *     "/register/confirm/{token}",
     *     name="user_email_confirm",
     *     methods={"GET"}
     * )
     *
     * @param string $token
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerConfirm(
      string $token,
      Request $request
    ) {
        $em = $this->getDoctrine()->getManager();

        /** @var \App\Entity\User $user */
        $user = $em->getRepository(User::class)
          ->findOneByConfirmationToken($token);

        if (!$user instanceof User || !$user->isConfirmationTokenNonExpired()) {
            $this->addFlash('danger', self::INVALID_TOKEN);

            return $this->redirectToRoute('home');
        }

        if (!$user->isEqualTo($this->getUser())) {
            $this->addFlash('danger', self::EMAIL_CONFIRM_USER_DIFF);

            return $this->redirectToRoute('home');
        }

        $event = new UserEvent($user);
        $this->eventDispatcher->dispatch(UserEvent::REGISTER_CONFIRM, $event);

        $em->flush();

        $this->addFlash('success', self::REGISTRATION_SUCCESS);

        return $this->guardAuthenticatorHandler
          ->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $this->userLoginAuthenticator,
            'main'
          );
    }

    /**
     * @Route(
     *     "/reset_password",
     *     name="user_reset_password",
     *     methods={"GET","POST"}
     * )
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resetPassword(Request $request)
    {
        $form = $this->createForm(UserResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)
              ->findOneByEmailOrUsername($formData['login_credential']);

            if (!$user) {
                $form->addError(new FormError(self::RESET_PASSWORD_REQUEST_FAIL));
            } else {
                $event = new UserEvent($user);
                $this->eventDispatcher
                  ->dispatch(UserEvent::RESET_PASSWORD_REQUEST, $event);

                $em->flush();

                $this->addFlash('success',
                  self::RESET_PASSWORD_REQUEST_SUCCESS);

            }
        }

        return $this->render('user/reset_password.html.twig', [
          'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(
     *     "/reset_password/confirm/{token}",
     *     name="reset_password_confirm",
     *     methods={"GET", "POST"}
     * )
     *
     * @param string $token
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @param \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $encoder
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function resetPasswordConfirm(
      string $token,
      Request $request,
      UserPasswordEncoderInterface $encoder
    ) {
        $em = $this->getDoctrine()->getManager();

        /** @var \App\Entity\User $user */
        $user = $em->getRepository(User::class)
          ->findOneByConfirmationToken($token);

        if (!$token || !$user instanceof User || !$user->isConfirmationTokenNonExpired()) {
            $this->addFlash('danger', self::INVALID_TOKEN);

            return $this->redirectToRoute('user_reset_password');
        }

        if (in_array(User::ROLE_USER_UNCONFIRMED, $user->getRoles())) {
            $this->addFlash('danger', self::RESET_PASSWORD_FAIL);

            return $this->redirectToRoute('user_reset_password');
        }

        $event = new UserEvent($user);
        $this->eventDispatcher
          ->dispatch(UserEvent::RESET_PASSWORD_CONFIRM, $event);

        $form = $this->createForm(UserNewPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $password = $encoder->encodePassword($user, $plainPassword);
            $user->setPassword($password);
            $em->flush();

            $this->addFlash('success', self::RESET_PASSWORD_SUCCESS);

            return $this->guardAuthenticatorHandler
              ->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $this->userLoginAuthenticator,
                'main'
              );
        }

        return $this->render('user/new_password.html.twig', [
          'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{username}", name="user_profile", methods={"GET"})
     *
     * @param \App\Entity\User $user
     *
     * @param \GuzzleHttp\Client $client
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profile(User $user, Client $client)
    {
        dd($client);
        return $this->render('user/profile.html.twig', ['user' => $user]);
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\UserEvent;
use App\FlashMessage\UserMessage as Flash;
use App\Form\UserGeneralType;
use App\Form\UserNewPasswordType;
use App\Form\UserResetPasswordType;
use App\Security\UserLoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class UserController extends AbstractController
{

    private $em;

    private $guardAuthenticatorHandler;

    private $userLoginAuthenticator;

    private $eventDispatcher;

    /**
     * UserController constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param GuardAuthenticatorHandler $guardAuthenticatorHandler
     * @param UserLoginAuthenticator $userLoginAuthenticator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
      EntityManagerInterface $em,
      GuardAuthenticatorHandler $guardAuthenticatorHandler,
      UserLoginAuthenticator $userLoginAuthenticator,
      EventDispatcherInterface $eventDispatcher
    ) {
        $this->em = $em;
        $this->guardAuthenticatorHandler = $guardAuthenticatorHandler;
        $this->userLoginAuthenticator = $userLoginAuthenticator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/register", name="user_register", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @param \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $encoder
     *
     * @return Response
     */
    public function register(
      Request $request,
      UserPasswordEncoderInterface $encoder
    ): Response {
        $user = new User();
        $form = $this->createForm(UserGeneralType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPassword = $encoder
              ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encodedPassword);

            $event = new UserEvent($user);
            $this->eventDispatcher
              ->dispatch(UserEvent::REGISTER_REQUEST, $event);

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', Flash::REGISTRATION_CONFIRMED);

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
     *     "/register/confirm/{confirmationToken}",
     *     name="user_email_confirm",
     *     methods={"GET"}
     * )
     *
     * @param User $user
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function registerConfirm(User $user): Response
    {
        if (!$user->isConfirmationTokenNonExpired()) {
            $this->addFlash('danger', Flash::INVALID_TOKEN);

            return $this->redirectToRoute('home_index');
        }

        if (!$user->isEqualTo($this->getUser())) {
            $this->addFlash('danger', Flash::EMAIL_CONFIRM_USER_DIFF);

            return $this->redirectToRoute('home_index');
        }

        $event = new UserEvent($user);
        $this->eventDispatcher->dispatch(UserEvent::REGISTER_CONFIRM, $event);

        $this->em->flush();

        $this->addFlash('success', Flash::REGISTRATION_SUCCESS);

        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * @Route(
     *     "/reset_password",
     *     name="user_reset_password",
     *     methods={"GET","POST"}
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function resetPassword(Request $request): Response
    {
        $form = $this->createForm(UserResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $user = $this->em->getRepository(User::class)
              ->findOneByEmailOrUsername($formData['login_credential']);

            if (!$user) {
                $form->addError(new FormError(Flash::RESET_PASSWORD_REQUEST_FAIL));
            } else {
                $event = new UserEvent($user);
                $this->eventDispatcher
                  ->dispatch(UserEvent::RESET_PASSWORD_REQUEST, $event);

                $this->em->flush();

                $this->addFlash('success',
                  Flash::RESET_PASSWORD_REQUEST_SUCCESS);
            }
        }

        return $this->render('user/reset_password.html.twig', [
          'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(
     *     "/reset_password/confirm/{confirmationToken}",
     *     name="reset_password_confirm",
     *     methods={"GET", "POST"}
     * )
     *
     * @param User $user
     * @param Request $request
     *
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function resetPasswordConfirm(
      User $user,
      Request $request,
      UserPasswordEncoderInterface $encoder
    ): Response {
        if (!$user->isConfirmationTokenNonExpired()) {
            $this->addFlash('danger', Flash::INVALID_TOKEN);

            return $this->redirectToRoute('user_reset_password');
        }

        if (in_array(User::ROLE_USER_UNCONFIRMED, $user->getRoles())) {
            $this->addFlash('danger', Flash::RESET_PASSWORD_FAIL);

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
            $this->em->flush();

            $this->addFlash('success', Flash::RESET_PASSWORD_SUCCESS);

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
     * @Route("/{username}", name="user_show", methods="GET")
     *
     * @param User $user
     *
     * @return Response
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/profile/edit", name="user_edit", methods={"GET|POST"})
     */
    public function update(
      Request $request,
      UserPasswordEncoderInterface $encoder
    ): Response {
        $user = $this->getUser();
        $email = $user->getEmail();
        $form = $this->createForm(UserGeneralType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getEmail() !== $email) {
                // Reset user roles.
                $user->setRoles([User::ROLE_USER_UNCONFIRMED]);

                $event = new UserEvent($user);
                $this->eventDispatcher->dispatch(
                  UserEvent::REGISTER_REQUEST,
                  $event
                );

                $this->addFlash('success', Flash::EMAIL_CHANGE_REQUESTED);
            }

            $plainPassword = $user->getPlainPassword();

            if (null !== $plainPassword) {
                $password = $encoder->encodePassword($user, $plainPassword);
                $user->setPassword($password);

                $this->addFlash('success', Flash::PASSWORD_CHANGED);
            }

            $this->em->flush();

            return $this->redirectToRoute('user_show', [
              'username' => $user->getUsername(),
            ]);
        }

        return $this->render('user/edit.html.twig', [
          'form' => $form->createView(),
        ]);
    }
}

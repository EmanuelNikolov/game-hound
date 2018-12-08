<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Event\UserEvent;
use App\FlashMessage\UserMessage as Flash;
use App\Form\UserGeneralType;
use App\Form\UserNewPasswordType;
use App\Form\UserResetPasswordType;
use App\Security\UserLoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
     * @param EntityManagerInterface $em
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
     * @Route("/signup", name="user_signup", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return Response
     */
    public function signUp(
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
              ->dispatch(UserEvent::SIGNUP_REQUEST, $event);

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', Flash::SIGNUP_SUCCESS);

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
     *     "/signup/confirm/{confirmationToken}",
     *     name="user_email_confirm",
     *     methods={"GET"}
     * )
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param User $user
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function emailConfirm(Request $request, User $user): Response
    {
        if (!$user->isConfirmationTokenNonExpired()) {
            $this->addFlash('danger', Flash::INVALID_TOKEN);

            return $this->redirectToRoute('home_index');
        }

        $event = new UserEvent($user);
        $this->eventDispatcher->dispatch(UserEvent::EMAIL_CONFIRM_REQUEST,
          $event);

        $this->em->flush();

        $this->addFlash('success', Flash::EMAIL_CONFIRM_SUCCESS);

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
     *     name="user_reset_password_confirm",
     *     methods={"GET", "POST"}
     * )
     *
     * @param User $user
     * @param Request $request
     *
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return Response
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
     * @param Request $request
     * @param User $user
     *
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    public function show(
      Request $request,
      User $user,
      PaginatorInterface $paginator
    ): Response {
        $pagination = $paginator->paginate(
          $user->getGameCollections(),
          $request->query->getInt('page', 1),
          GameCollectionController::PAGE_LIMIT
        );

        return $this->render('user/show.html.twig', [
          'user' => $user,
          'pagination' => $pagination,
        ]);
    }

    /**
     * @Route(
     *     "/profile/collections/{id}",
     *     name="user_show_collections",
     *     methods={"GET"}
     * )
     *
     * @param Request $request
     * @param Game $game
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    public function showCollections(
      Request $request,
      Game $game,
      PaginatorInterface $paginator
    ): Response {
        $pagination = $paginator->paginate(
          $this->getUser()->getGameCollections(),
          $request->query->getInt('page', 1),
          GameCollectionController::PAGE_LIMIT
        );

        return $this->render('user/show_collections.html.twig', [
          'game' => $game,
          'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/profile/edit", name="user_edit", methods={"GET|POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return Response
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
                  UserEvent::SIGNUP_REQUEST,
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

            return $this->guardAuthenticatorHandler
              ->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $this->userLoginAuthenticator,
                'main'
              );
        }

        return $this->render('user/edit.html.twig', [
          'form' => $form->createView(),
        ]);
    }
}

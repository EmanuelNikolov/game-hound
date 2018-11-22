<?php

namespace App\EventSubscriber;


use App\Entity\User;
use App\Event\UserEvent;
use App\Service\Mailer\MailerInterface;
use App\Utils\TokenCreatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EmailConfirmationSubscriber implements EventSubscriberInterface
{

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var TokenCreatorInterface
     */
    private $tokenCreator;

    /**
     * EmailConfirmationSubscriber constructor.
     *
     * @param MailerInterface $mailer
     * @param TokenCreatorInterface $tokenCreator
     */
    public function __construct(
      MailerInterface $mailer,
      TokenCreatorInterface $tokenCreator
    ) {
        $this->mailer = $mailer;
        $this->tokenCreator = $tokenCreator;
    }


    public static function getSubscribedEvents()
    {
        return [
          UserEvent::SIGNUP_REQUEST => 'handleSignUpRequest',
          UserEvent::EMAIL_CONFIRM_REQUEST => 'handleEmailConfirmRequest',
        ];
    }

    public function handleSignUpRequest(UserEvent $event)
    {
        $user = $event->getUser();
        $emailConfirmToken = $this->tokenCreator->createToken();
        $user->setConfirmationToken($emailConfirmToken);

        $dto = (new \DateTimeImmutable())->add(new \DateInterval('P1D'));
        $user->setConfirmationTokenRequestedAt($dto);

        $this->mailer->sendEmailConfirmationMessage($user);
    }

    public function handleEmailConfirmRequest(UserEvent $event)
    {
        $user = $event->getUser();
        $user->setConfirmationToken(null);
        $user->setConfirmationTokenRequestedAt(null);
        $user->setRoles([User::ROLE_USER_CONFIRMED]);
    }
}
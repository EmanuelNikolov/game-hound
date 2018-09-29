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
          UserEvent::REGISTER_REQUEST => 'handleRegisterRequest',
            UserEvent::REGISTER_CONFIRM => 'handleRegisterConfirm'
        ];
    }

    public function handleRegisterRequest(UserEvent $event)
    {
        $user = $event->getUser();
        $emailConfirmToken = $this->tokenCreator->createToken();
        $user->setConfirmationToken($emailConfirmToken);

        $this->mailer->sendEmailConfirmationMessage($user);
    }

    public function handleRegisterConfirm(UserEvent $event)
    {
        $user = $event->getUser();
        $user->setConfirmationToken(null);
        $user->setRoles([User::ROLE_USER_CONFIRMED]);
    }
}
<?php

namespace App\EventSubscriber;


use App\Event\UserRegisteredEvent;
use App\Service\Mailer\MailerInterface;
use App\Utils\TokenCreatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserRegisteredSubscriber implements EventSubscriberInterface
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
     * UserRegisteredSubscriber constructor.
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
          UserRegisteredEvent::NAME => 'sendConfirmationEmail',
        ];
    }

    public function sendConfirmationEmail(UserRegisteredEvent $event)
    {
        $user = $event->getUser();
        $emailConfirmToken = $this->tokenCreator->createToken();
        $user->setConfirmationToken($emailConfirmToken);

        $this->mailer->sendConfirmationEmail($user);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Emanuil
 * Date: 9/29/2018
 * Time: 10:48 AM
 */

namespace App\EventSubscriber;


use App\Event\UserEvent;
use App\Service\Mailer\MailerInterface;
use App\Utils\TokenCreatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ResetPasswordSubscriber implements EventSubscriberInterface
{

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var TokenCreatorInterface
     */
    private $tokenCreator;

    public function __construct(
      MailerInterface $mailer,
      TokenCreatorInterface $tokenCreator
    ) {
        $this->mailer = $mailer;
        $this->tokenCreator = $tokenCreator;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
          UserEvent::RESET_PASSWORD_REQUEST => 'handleResetPassword',
          UserEvent::RESET_PASSWORD_CONFIRM => 'handleResetPasswordConfirm',
        ];
    }

    public function handleResetPassword(UserEvent $event)
    {
        $user = $event->getUser();
        $resetPasswordToken = $this->tokenCreator->createToken();
        // TODO: put some expiration date on that password token
        $user->setConfirmationToken($resetPasswordToken);

        $this->mailer->sendPasswordResetMessage($user);
    }

    public function handleResetPasswordConfirm(UserEvent $event)
    {
        $user = $event->getUser();
        $user->setConfirmationToken(null);
    }
}
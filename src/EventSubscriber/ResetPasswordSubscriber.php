<?php
/**
 * Created by PhpStorm.
 * User: Emanuil
 * Date: 9/29/2018
 * Time: 10:48 AM
 */

namespace App\EventSubscriber;


use App\Entity\User;
use App\Event\UserEvent;
use App\FlashMessage\UserMessage;
use App\Service\Mailer\MailerInterface;
use App\Utils\TokenCreatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

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

    /**
     * @var Security
     */
    private $security;

    public function __construct(
      MailerInterface $mailer,
      TokenCreatorInterface $tokenCreator,
      Security $security
    ) {
        $this->mailer = $mailer;
        $this->tokenCreator = $tokenCreator;
        $this->security = $security;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
          UserEvent::RESET_PASSWORD_REQUEST => 'handleResetPasswordRequest',
          UserEvent::RESET_PASSWORD_CONFIRM => 'handleResetPasswordConfirm',
        ];
    }

    public function handleResetPasswordRequest(UserEvent $event)
    {
        $user = $event->getUser();

        if (in_array(User::ROLE_USER_UNCONFIRMED, $user->getRoles())) {
            $event->setFlashMessage(['danger', UserMessage::RESET_PASSWORD_FAIL]);
        } else {
            $resetPasswordToken = $this->tokenCreator->createToken();
            $user->setConfirmationToken($resetPasswordToken);

            $dto = (new \DateTimeImmutable())->add(new \DateInterval('P1D'));
            $user->setConfirmationTokenRequestedAt($dto);

            $this->mailer->sendPasswordResetMessage($user);
            $event->setFlashMessage(['success', UserMessage::RESET_PASSWORD_REQUEST_SUCCESS]);
        }
    }

    public function handleResetPasswordConfirm(UserEvent $event)
    {
        $user = $event->getUser();
        $user->setConfirmationToken(null);
        $user->setConfirmationTokenRequestedAt(null);
    }
}

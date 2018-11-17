<?php

namespace App\Service\Mailer;


use App\Entity\User;
use Symfony\Component\Routing\RouterInterface;

class Mailer implements MailerInterface
{

    private const CONFIRM_EMAIL_TEMPLATE = "email/confirmation.html.twig";

    private const PASSWORD_RESET_EMAIL_TEMPLATE = "email/password_reset.html.twig";

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var array
     */
    public $parameters;

    /**
     * Mailer constructor.
     *
     * @param \Twig_Environment $twig
     * @param \Swift_Mailer $mailer
     * @param RouterInterface $router
     * @param array $parameters
     */
    public function __construct(
      array $parameters,
      \Twig_Environment $twig,
      \Swift_Mailer $mailer,
      RouterInterface $router
    ) {
        $this->parameters = $parameters;
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->router = $router;
    }


    public function sendEmailConfirmationMessage(User $user)
    {
        $templateName = $this->parameters['confirmation'];
        $confirmationURL = $this->router->generate('user_email_confirm', [
          'confirmationToken' => $user->getConfirmationToken(),
        ]);

        $templateData = [
          'user' => $user,
          'confirmation_url' => $confirmationURL,
        ];

        $this->sendEmail(
          $this->parameters['from_email'],
          $user->getEmail(),
          $templateName,
          $templateData
        );
    }

    public function sendPasswordResetMessage(User $user)
    {
        $templateName = $this->parameters['reset_password'];
        $resetPasswordURL = $this->router->generate('user_reset_password', [
          'confirmationToken' => $user->getConfirmationToken(),
        ]);

        $templateData = [
          'user' => $user,
          'reset_password_url' => $resetPasswordURL,
        ];

        $this->sendEmail(
          $this->parameters['from_email'],
          $user->getEmail(),
          $templateName,
          $templateData
        );
    }

    private function sendEmail(
      string $from,
      string $to,
      string $templateName,
      array $templateData
    ) {
        $template = $this->twig->load($templateName);
        $subject = $template->renderBlock('subject');
        $body = $template->renderBlock('body_html', $templateData);

        // TODO: add simple text part, maybe template data object too?
        $message = (new \Swift_Message())
          ->setSubject($subject)
          ->setFrom($from)
          ->setTo($to)
          ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}
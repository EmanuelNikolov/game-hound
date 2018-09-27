<?php

namespace App\Service\Mailer;


use App\Entity\User;
use Symfony\Component\Routing\RouterInterface;

class Mailer implements MailerInterface
{

    private const CONFIRM_EMAIL_TEMPLATE = "email/confirmation.html.twig";

    private const PASSWORD_RESET_EMAIL_TEMPLATE = "email/password_reset.html.twig";

    private const FROM_EMAIL = "";

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
    private $parameters;

    /**
     * Mailer constructor.
     *
     * @param \Twig_Environment $twig
     * @param \Swift_Mailer $mailer
     * @param RouterInterface $router
//     * @param array $parameters
     */
    public function __construct(
      \Twig_Environment $twig,
      \Swift_Mailer $mailer,
      RouterInterface $router
//      array $parameters
    ) {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->router = $router;
//        $this->parameters = $parameters;
    }


    public function sendConfirmationEmail(User $user)
    {

    }

    public function sendPasswordResetEmail(User $user)
    {
        // TODO: Implement sendPasswordResetEmail() method.
    }

    private function sendEmail(string $from, string $to, string $templateName)
    {
        $template = $this->twig->load($templateName);
        $subject = $template->renderBlock('subject');
        $body = $template->renderBlock('body_html');

        $message = (new \Swift_Message())
          ->setSubject($subject)
          ->setFrom($from)
          ->setTo($to)
          ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}
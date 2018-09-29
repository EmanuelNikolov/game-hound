<?php

namespace App\Service\Mailer;


use App\Entity\User;

interface MailerInterface
{

    public function sendEmailConfirmationMessage(User $user);

    public function sendPasswordResetMessage(User $user);
}
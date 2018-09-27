<?php

namespace App\Service\Mailer;


use App\Entity\User;

interface MailerInterface
{

    public function sendConfirmationEmail(User $user);

    public function sendPasswordResetEmail(User $user);
}
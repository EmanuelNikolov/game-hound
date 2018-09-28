<?php

namespace App\Security;


use App\Entity\User;
use App\Exception\AccountInactiveException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    /**
     * Checks the user account before authentication.
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     */
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }
    }

    /**
     * Checks the user account after authentication.
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     */
    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isActive()) {
            throw new AccountInactiveException("You have to verify your email before viewing this page");
        }
    }
}
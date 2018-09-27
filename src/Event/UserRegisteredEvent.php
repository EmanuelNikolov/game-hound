<?php

namespace App\Event;


use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class UserRegisteredEvent extends Event
{

    public const NAME = 'user.registered';

    /**
     * @var User
     */
    private $user;

    /**
     * UserRegisteredEvent constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return UserRegisteredEvent
     */
    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }


}
<?php

namespace App\Security\Voter;

use App\Entity\GameCollection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class GameCollectionVoter extends Voter
{

    public const EDIT = 'GAME_COLLECTION_EDIT';

    protected function supports($attribute, $subject)
    {
        return ($attribute === self::EDIT) && $subject instanceof GameCollection;
    }

    /**
     * @param string $attribute
     * @param GameCollection $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute(
      $attribute,
      $subject,
      TokenInterface $token
    ) {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return $this->canEdit($subject, $user);

        // throw new \LogicException("This shouldn't be reached.");
    }

    private function canEdit(GameCollection $collection, UserInterface $user): bool
    {
        return $collection->getUser() === $user;
    }
}

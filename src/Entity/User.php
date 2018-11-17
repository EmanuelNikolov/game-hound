<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 *
 * @UniqueEntity(
 *     "email",
 *     message="This email is already taken",
 *     groups={"edit"}
 * )
 * @UniqueEntity(
 *     "username",
 *     message="This username is already taken",
 *     groups={"edit"}
 * )
 */
class User implements UserInterface, EquatableInterface
{

    public const ROLE_USER_UNCONFIRMED = "ROLE_USER_UNCONFIRMED";

    public const ROLE_USER_CONFIRMED = "ROLE_USER_CONFIRMED";

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(
     *     message = "'{{ value }}' is not a valid email.",
     *     checkMX = true,
     *     groups={"edit"}
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=25)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 5,
     *      max = 25,
     *      minMessage = "Username must be at least {{ limit }} characters
     *   long", maxMessage = "Username cannot be longer than {{ limit }}
     *   characters"
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 8,
     *      max = 50,
     *      minMessage = "Password must be at least {{ limit }} characters
     *   long", maxMessage = "Password cannot be longer than {{ limit }}
     *   characters", groups={"edit"}
     * )
     */
    private $plainPassword;

    /**
     * @var null|string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $confirmationToken;

    /**
     * @var null|string
     *
     * @ORM\Column(type="datetimetz_immutable", nullable=true)
     */
    private $confirmationTokenRequestedAt;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="json")
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GameCollection", mappedBy="user")
     */
    private $gameCollections;

    public function __construct()
    {
        $this->roles = [self::ROLE_USER_UNCONFIRMED];
        $this->active = true;
        $this->gameCollections = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     *
     * @return User
     */
    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    /**
     * @param string $confirmationToken
     *
     * @return User
     * @throws \Exception
     */
    public function setConfirmationToken(
      ?string $confirmationToken
    ): User {
        $this->confirmationToken = $confirmationToken;
        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getConfirmationTokenRequestedAt(): ?\DateTimeImmutable
    {
        return $this->confirmationTokenRequestedAt;
    }

    /**
     * @param \DateTimeImmutable|null $confirmationTokenRequestedAt
     *
     * @return User
     */
    public function setConfirmationTokenRequestedAt(
      ?\DateTimeImmutable $confirmationTokenRequestedAt
    ): User {
        $this->confirmationTokenRequestedAt = $confirmationTokenRequestedAt;
        return $this;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isConfirmationTokenNonExpired(): bool
    {
        $dto = $this->confirmationTokenRequestedAt;

        return ($dto instanceof \DateTimeImmutable) &&
          ($dto > new \DateTimeImmutable('now'));
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return User
     */
    public function setActive(bool $active): User
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Returns the roles granted to the user.
     *
     * @return array (Role|string)[] The user roles
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantee every user at least has a role
        if (empty($roles)) {
            $roles[] = self::ROLE_USER_UNCONFIRMED;
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        return true;
    }

    /**
     * @return Collection|GameCollection[]
     */
    public function getGameCollections(): Collection
    {
        return $this->gameCollections;
    }

    public function addGameCollection(GameCollection $gameCollection): self
    {
        if (!$this->gameCollections->contains($gameCollection)) {
            $this->gameCollections[] = $gameCollection;
            $gameCollection->setUser($this);
        }

        return $this;
    }

    public function removeGameCollection(GameCollection $gameCollection): self
    {
        if ($this->gameCollections->contains($gameCollection)) {
            $this->gameCollections->removeElement($gameCollection);
            // set the owning side to null (unless already changed)
            if ($gameCollection->getUser() === $this) {
                $gameCollection->setUser(null);
            }
        }

        return $this;
    }
}

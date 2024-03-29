<?php

declare(strict_types=1);

namespace App\Security;

use App\Module\User\Entity\User\Status;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserIdentity
 * @package App\Security
 */
class UserIdentity implements UserInterface, EquatableInterface
{
    private string $id;
    private string $username;
    private string $password;
    private string $display;
    private string $role;
    private string $status;

    /**
     * UserIdentity constructor.
     * @param string $id
     * @param string $username
     * @param string $password
     * @param string $display
     * @param string $role
     * @param string $status
     */
    public function __construct(
        string $id,
        string $username,
        string $password,
        string $display,
        string $role,
        string $status
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->display = $display;
        $this->role = $role;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === Status::ACTIVE;
    }

    /**
     * @return string
     */
    public function getDisplay(): string
    {
        return $this->display;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return [$this->role];
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {

    }

    /**
     * @param UserInterface $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        return
            $this->id === $user->id &&
            $this->password === $user->password &&
            $this->role === $user->role &&
            $this->status === $user->status;
    }
}

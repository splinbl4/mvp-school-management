<?php

declare(strict_types=1);

namespace App\Tests\Builder\User;

use App\Module\Company\Entity\Company\Company;
use App\Module\Company\Entity\Company\Id as CompanyId;
use App\Module\User\Entity\User\Email;
use App\Module\User\Entity\User\Id;
use App\Module\User\Entity\User\Name;
use App\Module\User\Entity\User\Role;
use App\Module\User\Entity\User\Token;
use App\Module\User\Entity\User\User;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

/**
 * Class UserBuilder
 * @package App\Tests\Builder\User
 */
class UserBuilder
{
    private Id $id;
    private DateTimeImmutable $date;
    private Name $name;
    private Email $email;
    private string $hash;
    private Token $joinConfirmToken;
    private bool $active = false;
    private bool $isCreate = false;

    /**
     * UserBuilder constructor.
     */
    public function __construct()
    {
        $this->id = Id::generate();
        $this->date = new DateTimeImmutable();
        $this->name = new Name('First', 'Last');
        $this->email = new Email('mail@app.test');
        $this->hash = 'hash';
        $this->joinConfirmToken = new Token(Uuid::uuid4()->toString(), $this->date->modify('+1 day'));
    }

    /**
     * @param Token $token
     * @return $this
     */
    public function withJoinConfirmToken(Token $token): self
    {
        $clone = clone $this;
        $clone->joinConfirmToken = $token;

        return $clone;
    }

    /**
     * @param Email $email
     * @return $this
     */
    public function withEmail(Email $email): self
    {
        $clone = clone $this;
        $clone->email = $email;

        return $clone;
    }

    /**
     * @return $this
     */
    public function active(): self
    {
        $clone = clone $this;
        $clone->active = true;
        return $clone;
    }

    /**
     * @return $this
     */
    public function viaCreate(): self
    {
        $clone = clone $this;
        $clone->isCreate = true;
        return $clone;
    }

    /**
     * @return User
     */
    public function build(): User
    {
        $company = new Company(CompanyId::generate());

        if ($this->isCreate) {
            return User::create(
                $this->id,
                $this->date,
                $this->name,
                Role::user(),
                $company,
                $this->email
            );
        }

        $user = User::joinByEmail(
            $this->id,
            $this->date,
            $this->name,
            Role::owner(),
            $company,
            $this->email,
            $this->hash,
            $this->joinConfirmToken
        );

        if ($this->active) {
            $user->confirmJoin(
                $this->joinConfirmToken->getValue(),
                $this->joinConfirmToken->getExpires()->modify('-1 day')
            );
        }

        return $user;
    }
}

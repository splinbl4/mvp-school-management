<?php

declare(strict_types=1);

namespace App\Module\User\Entity\User;

use Webmozart\Assert\Assert;

/**
 * Class Role
 * @package App\Module\User\Entity\User
 */
class Role
{
    public const USER = 'ROLE_USER';
    public const TEACHER = 'ROLE_TEACHER';
    public const ADMIN = 'ROLE_ADMIN';
    public const OWNER = 'ROLE_OWNER';

    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::USER,
            self::TEACHER,
            self::ADMIN,
            self::OWNER,
        ]);

        $this->name = $name;
    }

    public static function user(): self
    {
        return new self(self::USER);
    }

    public static function teacher(): self
    {
        return new self(self::TEACHER);
    }

    public static function admin(): self
    {
        return new self(self::ADMIN);
    }

    public static function owner(): self
    {
        return new self(self::OWNER);
    }

    public function getName(): string
    {
        return $this->name;
    }
}

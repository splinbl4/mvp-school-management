<?php

declare(strict_types=1);

namespace App\Module\User\Entity\User;

use Webmozart\Assert\Assert;

/**
 * Class Status
 * @package App\Module\User\Entity\User
 */
class Status
{
    public const WAIT = 'wait';
    public const ACTIVE = 'active';

    private string $name;

    private static array $statusNameMap = [
        self::WAIT => 'Wait',
        self::ACTIVE => 'Active'
    ];

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::WAIT,
            self::ACTIVE
        ]);
        $this->name = $name;
    }

    public static function wait(): self
    {
        return new self(self::WAIT);
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public function isWait(): bool
    {
        return $this->name === self::WAIT;
    }

    public function isActive(): bool
    {
        return $this->name === self::ACTIVE;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDisplayName(): string
    {
        return self::$statusNameMap[$this->name];
    }
}

<?php

namespace App\Tests\Unit\Module\User\Command;

use App\Module\User\Service\PasswordHasher;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class ChangePasswordTest
 * @package App\Tests\Unit\Module\User\Command
 */
class ChangePasswordTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->active()->build();
        $hash = 'new-hash';
        $hasher = $this->createHasher(true, $hash);

        $user->changePassword('old-password', 'new-password', $hasher);

        self::assertEquals($user->getPasswordHash(), $hash);
    }

    public function testWrongCurrent(): void
    {
        $user = (new UserBuilder())->active()->build();
        $hash = 'new-hash';
        $hasher = $this->createHasher(false, $hash);

        $this->expectExceptionMessage('Incorrect current password.');

        $user->changePassword('wrong-old-password', 'new-password', $hasher);
    }

    public function testByCreate(): void
    {
        $user = (new UserBuilder())->viaCreate()->build();

        $hasher = $this->createHasher(false, 'new-hash');

        $this->expectExceptionMessage('User does not have an old password.');
        $user->changePassword(
            'any-old-password',
            'new-password',
            $hasher
        );
    }

    private function createHasher(bool $valid, string $hash): PasswordHasher
    {
        $hasher = $this->createStub(PasswordHasher::class);
        $hasher->method('validate')->willReturn($valid);
        $hasher->method('hash')->willReturn($hash);
        return $hasher;
    }
}

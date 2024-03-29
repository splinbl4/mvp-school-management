<?php

namespace App\Tests\Unit\Module\User\Command\ResetPassword;

use App\Module\User\Entity\User\Token;
use App\Module\User\Service\PasswordHasher;
use App\Tests\Builder\User\UserBuilder;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * Class ResetTest
 * @package App\Tests\Unit\Module\User\Command\ResetPassword
 */
class ResetTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));
        $hash = 'hash';
        $hasher = $this->createHasher(true, $hash);

        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getPasswordResetToken());

        $user->resetPassword($token->getValue(), 'new-password', $now, $hasher);

        self::assertNull($user->getPasswordResetToken());
        self::assertEquals($hash, $user->getPasswordHash());
    }

    public function testInvalidToken(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));
        $hash = 'hash';
        $hasher = $this->createHasher(true, $hash);

        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getPasswordResetToken());
        $this->expectExceptionMessage('Token is invalid.');

        $user->resetPassword(Uuid::uuid4()->toString(), 'new-password', $now, $hasher);
    }

    public function testExpiredToken(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));
        $hash = 'hash';
        $hasher = $this->createHasher(true, $hash);

        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getPasswordResetToken());
        $this->expectExceptionMessage('Token is expired.');

        $user->resetPassword($token->getValue(), 'new-password', $now->modify('+1 day'), $hasher);
    }

    public function testNotRequested(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));
        $hash = 'hash';
        $hasher = $this->createHasher(true, $hash);

        $this->expectExceptionMessage('Resetting is not requested.');

        $user->resetPassword($token->getValue(), 'new-password', $now, $hasher);
    }

    private function createToken(DateTimeImmutable $date): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            $date
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

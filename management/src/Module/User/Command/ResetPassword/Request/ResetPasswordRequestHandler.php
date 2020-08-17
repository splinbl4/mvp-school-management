<?php

declare(strict_types=1);

namespace App\Module\User\Command\ResetPassword\Request;

use App\Module\Flusher;
use App\Module\User\Entity\User\Email;
use App\Module\User\Repository\UserRepositoryInterface;
use App\Module\User\Service\PasswordResetTokenSender;
use App\Module\User\Service\Tokenizer;
use DateTimeImmutable;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class Handler
 * @package App\Module\User\Command\ResetPassword\Request
 */
class ResetPasswordRequestHandler
{
    private UserRepositoryInterface $userRepository;
    private Tokenizer $tokenizer;
    private Flusher $flusher;
    private PasswordResetTokenSender $sender;

    /**
     * Handler constructor.
     * @param UserRepositoryInterface $userRepository
     * @param Tokenizer $tokenizer
     * @param Flusher $flusher
     * @param PasswordResetTokenSender $sender
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        Tokenizer $tokenizer,
        Flusher $flusher,
        PasswordResetTokenSender $sender
    ) {
        $this->userRepository = $userRepository;
        $this->tokenizer = $tokenizer;
        $this->flusher = $flusher;
        $this->sender = $sender;
    }

    /**
     * @param ResetPasswordRequestCommand $command
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function handle(ResetPasswordRequestCommand $command): void
    {
        $email = new Email($command->email);
        $user = $this->userRepository->getByEmail($email);
        $date = new DateTimeImmutable();
        $token = $this->tokenizer->generate($date);

        $user->requestPasswordReset($token, $date);

        $this->flusher->flush();

        $this->sender->send($email, $token);
    }
}

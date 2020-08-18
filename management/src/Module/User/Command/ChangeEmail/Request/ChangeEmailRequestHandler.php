<?php

declare(strict_types=1);

namespace App\Module\User\Command\ChangeEmail\Request;

use App\Module\Flusher;
use App\Module\User\Entity\User\Email;
use App\Module\User\Entity\User\Id;
use App\Module\User\Repository\UserRepositoryInterface;
use App\Module\User\Service\NewEmailConfirmTokenSender;
use App\Module\User\Service\Tokenizer;
use DateTimeImmutable;
use DomainException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class Handler
 * @package App\Module\User\Command\ChangeEmail\Request
 */
class ChangeEmailRequestHandler
{
    private UserRepositoryInterface $userRepository;

    private Tokenizer $tokenizer;

    private NewEmailConfirmTokenSender $sender;

    private Flusher $flusher;

    /**
     * ChangeEmailRequestHandler constructor.
     * @param UserRepositoryInterface $userRepository
     * @param Tokenizer $tokenizer
     * @param NewEmailConfirmTokenSender $sender
     * @param Flusher $flusher
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        Tokenizer $tokenizer,
        NewEmailConfirmTokenSender $sender,
        Flusher $flusher
    ) {
        $this->userRepository = $userRepository;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
        $this->flusher = $flusher;
    }

    /**
     * @param ChangeEmailRequestCommand $command
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function handle(ChangeEmailRequestCommand $command): void
    {
        $user = $this->userRepository->get(new Id($command->id));
        $email = new Email($command->email);

        if ($this->userRepository->hasByEmail($email)) {
            throw new DomainException('Email is already in use.');
        }

        $date = new DateTimeImmutable();
        $token = $this->tokenizer->generate($date);

        $user->requestEmailChanging($token, $date, $email);

        $this->flusher->flush();

        $this->sender->send($email, $token);
    }
}

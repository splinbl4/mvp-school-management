<?php

declare(strict_types=1);

namespace App\Module\User\Command\ChangeEmail\Confirm;

use App\Module\Flusher;
use App\Module\User\Entity\User\Id;
use App\Module\User\Repository\UserRepositoryInterface;
use DateTimeImmutable;

/**
 * Class Handler
 * @package App\Module\User\Command\ChangeEmail\Confirm
 */
class ChangeEmailConfirmHandler
{
    private UserRepositoryInterface $userRepository;

    private Flusher $flusher;

    public function __construct(UserRepositoryInterface $userRepository, Flusher $flusher)
    {
        $this->userRepository = $userRepository;
        $this->flusher = $flusher;
    }

    public function handle(ChangeEmailConfirmCommand $command): void
    {
        $user = $this->userRepository->get(new Id($command->id));

        $user->confirmEmailChanging($command->token, new DateTimeImmutable());

        $this->flusher->flush();
    }
}

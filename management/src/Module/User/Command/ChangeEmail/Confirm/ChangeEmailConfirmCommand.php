<?php

declare(strict_types=1);

namespace App\Module\User\Command\ChangeEmail\Confirm;

/**
 * Class Command
 * @package App\Module\User\Command\ChangeEmail\Confirm
 */
class ChangeEmailConfirmCommand
{
    public string $id;
    public string $token;

    public function __construct(string $id, string $token)
    {
        $this->id = $id;
        $this->token = $token;
    }
}

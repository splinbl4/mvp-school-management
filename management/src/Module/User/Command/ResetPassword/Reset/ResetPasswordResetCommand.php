<?php

declare(strict_types=1);

namespace App\Module\User\Command\ResetPassword\Reset;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Command
 * @package App\Module\User\Command\ResetPassword\Reset
 */
class ResetPasswordResetCommand
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public string $token = '';

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=6)
     */
    public string $password = '';

    public function __construct(string $token)
    {
        $this->token = $token;
    }
}

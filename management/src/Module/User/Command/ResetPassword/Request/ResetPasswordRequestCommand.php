<?php

declare(strict_types=1);

namespace App\Module\User\Command\ResetPassword\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Command
 * @package App\Module\User\Command\ResetPassword\Request
 */
class ResetPasswordRequestCommand
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public string $email = '';
}

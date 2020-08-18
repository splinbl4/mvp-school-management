<?php

declare(strict_types=1);

namespace App\Module\User\Command\ChangeEmail\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Command
 * @package App\Module\User\Command\ChangeEmail\Request
 */
class ChangeEmailRequestCommand
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public string $id = '';

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public string $email = '';

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}

<?php

declare(strict_types=1);

namespace App\Module\User\ReadModel;

/**
 * Class AuthView
 * @package App\Module\User\ReadModel
 */
class AuthView
{
    public string $id;
    public string $email;
    public string $password_hash;
    public string $name;
    public string $role;
    public string $status;
}

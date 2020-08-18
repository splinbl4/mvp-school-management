<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Module\Company\Entity\Company\Company;
use App\Module\Company\Entity\Company\Id;
use App\Module\User\Entity\User\Email;
use App\Module\User\Entity\User\Id as IdUser;
use App\Module\User\Entity\User\Name;
use App\Module\User\Entity\User\Role;
use App\Module\User\Entity\User\User;
use App\Module\User\Service\PasswordHasher;
use App\Module\User\Service\Tokenizer;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class UserFixtures
 * @package App\DataFixtures
 */
class UserFixtures extends Fixture
{
    private PasswordHasher $hasher;
    private Tokenizer $tokenizer;

    /**
     * UserFixtures constructor.
     * @param PasswordHasher $hasher
     * @param Tokenizer $tokenizer
     */
    public function __construct(PasswordHasher $hasher, Tokenizer $tokenizer)
    {
        $this->hasher = $hasher;
        $this->tokenizer = $tokenizer;
    }

    public function load(ObjectManager $manager)
    {
        $company = new Company(Id::generate());
        $hash = $this->hasher->hash('password');
        $date = new DateTimeImmutable();
        $token = $this->tokenizer->generate($date);

        $user = User::joinByEmail(
            IdUser::generate(),
            $date,
            new Name('Сергей', 'Быков'),
            Role::owner(),
            $company,
            new Email('admin@app.ru'),
            $hash,
            $token
        );

        $user->confirmJoin($token->getValue(), $date);
        $manager->persist($company);
        $manager->persist($user);
        $manager->flush();
    }
}

<?php

declare(strict_types=1);

namespace App\Module\User\Command\ChangeEmail\Request;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ChangeEmailConfirmForm
 * @package App\Module\User\Command\ChangeEmail\Confirm
 */
class ChangeEmailRequestForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChangeEmailRequestCommand::class,
        ]);
    }
}

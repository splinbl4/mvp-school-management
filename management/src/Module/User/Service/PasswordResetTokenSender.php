<?php

declare(strict_types=1);

namespace App\Module\User\Service;

use App\Module\User\Entity\User\Email;
use App\Module\User\Entity\User\Token;
use RuntimeException;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class PasswordResetTokenSender
 * @package App\Module\User\Service
 */
class PasswordResetTokenSender
{
    private Swift_Mailer $mailer;
    private Environment $twig;

    public function __construct(Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param Email $email
     * @param Token $token
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function send(Email $email, Token $token): void
    {
        $message = (new Swift_Message('Password Reset'))
            ->setTo($email->getValue())
            ->setBody($this->twig->render('mail/user/reset.html.twig', ['token' => $token->getValue()]), 'text/html');

        if ($this->mailer->send($message) === 0) {
            throw new RuntimeException('Unable to send email.');
        }
    }
}

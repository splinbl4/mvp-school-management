<?php

declare(strict_types=1);

namespace App\Controller\Auth\Join;

use App\Controller\ErrorHandler;
use App\Module\User\Command\JoinByEmail\Confirm\JoinByEmailConfirmCommand;
use App\Module\User\Command\JoinByEmail\Confirm\JoinByEmailConfirmHandler;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConfirmAction
 * @package App\Controller\Auth\Join
 */
class ConfirmController extends AbstractController
{
    private ErrorHandler $errorHandler;

    /**
     * JoinConfirm constructor.
     * @param ErrorHandler $errorHandler
     */
    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    /**
     * @Route("/signup/{token}", name="auth.join.confirm")
     * @param string $token
     * @param JoinByEmailConfirmHandler $handler
     * @return Response
     */
    public function handle(string $token, JoinByEmailConfirmHandler $handler): Response
    {
        $command = new JoinByEmailConfirmCommand($token);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Email успешно подтвержден!');
            return $this->redirectToRoute('home');
        } catch (DomainException $exception) {
            $this->errorHandler->handle($exception);
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('auth.join');
        }
    }
}

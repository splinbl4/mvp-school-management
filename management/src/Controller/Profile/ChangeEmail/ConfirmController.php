<?php

declare(strict_types=1);

namespace App\Controller\Profile\ChangeEmail;

use App\Controller\ErrorHandler;
use App\Module\User\Command\ChangeEmail\Confirm\ChangeEmailConfirmCommand;
use App\Module\User\Command\ChangeEmail\Confirm\ChangeEmailConfirmHandler;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConfirmController
 * @package App\Controller\Profile\ChangeEmail
 */
class ConfirmController extends AbstractController
{
    private ErrorHandler $errorHandler;

    /**
     * ConfirmController constructor.
     * @param ErrorHandler $errorHandler
     */
    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    /**
     * @Route("/profile/email/{token}", name="profile.email.confirm")
     * @param string $token
     * @param ChangeEmailConfirmHandler $handler
     * @return Response
     */
    public function handle(string $token, ChangeEmailConfirmHandler $handler): Response
    {
        $command = new ChangeEmailConfirmCommand($this->getUser()->getId(), $token);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Email is successfully changed.');
            return $this->redirectToRoute('profile');
        } catch (DomainException $exception) {
            $this->errorHandler->handle($exception);
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('profile');
        }
    }
}

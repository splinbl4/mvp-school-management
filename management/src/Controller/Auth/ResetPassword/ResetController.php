<?php

declare(strict_types=1);

namespace App\Controller\Auth\ResetPassword;

use App\Controller\ErrorHandler;
use App\Module\User\Command\ResetPassword\Reset\ResetPasswordResetCommand;
use App\Module\User\Command\ResetPassword\Reset\ResetPasswordResetForm;
use App\Module\User\Command\ResetPassword\Reset\ResetPasswordResetHandler;
use App\Module\User\ReadModel\UserFetcher;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetController extends AbstractController
{
    private ErrorHandler $errorHandler;
    private UserFetcher $fetcher;

    /**
     * RequestAction constructor.
     * @param ErrorHandler $errorHandler
     * @param UserFetcher $fetcher
     */
    public function __construct(ErrorHandler $errorHandler, UserFetcher $fetcher)
    {
        $this->errorHandler = $errorHandler;
        $this->fetcher = $fetcher;
    }

    /**
     * @Route("/reset/{token}", name="auth.reset.reset")
     * @param string $token
     * @param Request $request
     * @param ResetPasswordResetHandler $handler
     * @return Response
     */
    public function handle(string $token, Request $request, ResetPasswordResetHandler $handler): Response
    {
        if (!$this->fetcher->existsByResetToken($token)) {
            $this->addFlash('error', 'Incorrect or already confirmed token.');
            return $this->redirectToRoute('home');
        }

        $command = new ResetPasswordResetCommand($token);

        $form = $this->createForm(ResetPasswordResetForm::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Password is successfully changed.');
                return $this->redirectToRoute('home');
            } catch (DomainException $exception) {
                $this->errorHandler->handle($exception);
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('app/auth/reset/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
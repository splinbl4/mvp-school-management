<?php

declare(strict_types=1);

namespace App\Controller\Auth\ResetPassword;

use App\Controller\ErrorHandler;
use App\Module\User\Command\ResetPassword\Request\ResetPasswordRequestCommand;
use App\Module\User\Command\ResetPassword\Request\ResetPasswordRequestForm;
use App\Module\User\Command\ResetPassword\Request\ResetPasswordRequestHandler;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class RequestAction
 * @package App\Controller\Auth\ResetPassword
 */
class RequestController extends AbstractController
{
    private ErrorHandler $errorHandler;

    /**
     * RequestAction constructor.
     * @param ErrorHandler $errorHandler
     */
    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    /**
     * @Route("/reset", name="auth.reset")
     * @param Request $request
     * @param ResetPasswordRequestHandler $handle
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function handle(Request $request, ResetPasswordRequestHandler $handle): Response
    {
        $command = new ResetPasswordRequestCommand();

        $form = $this->createForm(ResetPasswordRequestForm::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handle->handle($command);
                $this->addFlash('success', 'Check your email.');
                return $this->redirectToRoute('home');
            } catch (DomainException $exception) {
                $this->errorHandler->handle($exception);
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('app/auth/reset/request.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

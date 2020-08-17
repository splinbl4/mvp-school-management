<?php

declare(strict_types=1);

namespace App\Controller\Auth\Join;

use App\Controller\ErrorHandler;
use App\Module\User\Command\JoinByEmail\Request\JoinByEmailRequestCommand;
use App\Module\User\Command\JoinByEmail\Request\JoinByEmailRequestForm;
use App\Module\User\Command\JoinByEmail\Request\JoinByEmailRequestHandler;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class JoinRequest
 * @package App\Controller\Auth\Join
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
     * @Route("/register", name="auth.join")
     *
     * @param Request $request
     * @param JoinByEmailRequestHandler $handler
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function handle(Request $request, JoinByEmailRequestHandler $handler): Response
    {
        $command = new JoinByEmailRequestCommand();

        $form = $this->createForm(JoinByEmailRequestForm::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Check your email.');
                return $this->redirectToRoute('home');
            } catch (DomainException $exception) {
                $this->errorHandler->handle($exception);
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('app/auth/join.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

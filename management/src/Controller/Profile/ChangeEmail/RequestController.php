<?php

declare(strict_types=1);

namespace App\Controller\Profile\ChangeEmail;

use App\Controller\ErrorHandler;
use App\Module\User\Command\ChangeEmail\Request\ChangeEmailRequestCommand;
use App\Module\User\Command\ChangeEmail\Request\ChangeEmailRequestForm;
use App\Module\User\Command\ChangeEmail\Request\ChangeEmailRequestHandler;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RequestController
 * @package App\Controller\Profile\ChangeEmail
 */
class RequestController extends AbstractController
{
    private ErrorHandler $errorHandler;

    /**
     * RequestController constructor.
     * @param ErrorHandler $errorHandler
     */
    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    /**
     * @Route("/profile/email", name="profile.email")
     * @param Request $request
     * @param ChangeEmailRequestHandler $handler
     * @return Response
     */
    public function handle(Request $request, ChangeEmailRequestHandler $handler): Response
    {
        $command = new ChangeEmailRequestCommand($this->getUser()->getId());

        $form = $this->createForm(ChangeEmailRequestForm::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Check your email.');
                return $this->redirectToRoute('profile');
            } catch (DomainException $exception) {
                $this->errorHandler->handle($exception);
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('app/profile/email.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Auth\Join;

use App\Module\User\Command\JoinByEmail\Request\JoinByEmailRequestCommand;
use App\Module\User\Command\JoinByEmail\Request\JoinByEmailRequestForm;
use App\Module\User\Command\JoinByEmail\Request\JoinByEmailRequestHandler;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class JoinRequest
 * @package App\Controller\Auth\Join
 */
class JoinRequest extends AbstractController
{
    /**
     * @Route("/auth/join", name="auth.join")
     *
     * @param Request $request
     * @param JoinByEmailRequestHandler $handler
     * @return Response
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
            } catch (DomainException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/auth/join.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

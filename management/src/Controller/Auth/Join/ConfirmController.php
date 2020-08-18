<?php

declare(strict_types=1);

namespace App\Controller\Auth\Join;

use App\Controller\ErrorHandler;
use App\Module\User\Command\JoinByEmail\Confirm\JoinByEmailConfirmCommand;
use App\Module\User\Command\JoinByEmail\Confirm\JoinByEmailConfirmHandler;
use App\Module\User\Repository\UserRepositoryInterface;
use App\Security\LoginFormAuthenticator;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

/**
 * Class ConfirmAction
 * @package App\Controller\Auth\Join
 */
class ConfirmController extends AbstractController
{
    private ErrorHandler $errorHandler;
    /**
     * @var UserProviderInterface
     */
    private UserProviderInterface $userProvider;
    /**
     * @var GuardAuthenticatorHandler
     */
    private GuardAuthenticatorHandler $guardHandler;
    /**
     * @var LoginFormAuthenticator
     */
    private LoginFormAuthenticator $authenticator;
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $userRepository;

    /**
     * JoinConfirm constructor.
     * @param ErrorHandler $errorHandler
     * @param UserProviderInterface $userProvider
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        ErrorHandler $errorHandler,
        UserProviderInterface $userProvider,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator,
        UserRepositoryInterface $userRepository
    ) {
        $this->errorHandler = $errorHandler;
        $this->userProvider = $userProvider;
        $this->guardHandler = $guardHandler;
        $this->authenticator = $authenticator;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/register/{token}", name="auth.join.confirm")
     * @param string $token
     * @param Request $request
     * @param JoinByEmailConfirmHandler $handler
     * @return Response
     */
    public function handle(string $token, Request $request, JoinByEmailConfirmHandler $handler): Response
    {
        $command = new JoinByEmailConfirmCommand($token);

        if (!$user = $this->userRepository->findByJoinConfirmToken($command->token)) {
            $this->addFlash('error', 'Incorrect or already confirmed token.');
            return $this->redirectToRoute('auth.join');
        }

        try {
            $handler->handle($command);
            return $this->guardHandler->authenticateUserAndHandleSuccess(
                $this->userProvider->loadUserByUsername($user->getEmail()->getValue()),
                $request,
                $this->authenticator,
                'main'
            );
        } catch (DomainException $exception) {
            $this->errorHandler->handle($exception);
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('auth.join');
        }
    }
}

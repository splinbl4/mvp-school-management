<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Module\User\Entity\User\Id;
use App\Module\User\Repository\DoctrineOrm\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ShowController
 * @package App\Controller\Profile
 */
class ShowController extends AbstractController
{
    private UserRepository $repository;

    /**
     * ShowController constructor.
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/profile", name="profile")
     * @return Response
     */
    public function handle(): Response
    {
        $user = $this->repository->get(new Id($this->getUser()->getId()));

        return $this->render('app/profile/show.html.twig', compact('user'));
    }
}

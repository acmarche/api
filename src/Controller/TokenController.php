<?php

namespace AcMarche\Api\Controller;

use AcMarche\Api\Entity\User;
use AcMarche\Api\Security\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/token')]
#[IsGranted('ROLE_API_ADMIN')]
class TokenController extends AbstractController
{
    public function __construct(
        private readonly TokenGenerator $tokenGenerator,
    ) {}

    #[Route(path: '/{id}', name: 'api_token_generate', methods: ['GET'])]
    public function index(User $user): Response
    {
        $token = $this->tokenGenerator->generateToken($user);

        return new JsonResponse(['token' => $token]);
    }
}
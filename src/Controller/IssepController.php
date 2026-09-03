<?php

namespace AcMarche\Api\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/issep')]
class IssepController extends AbstractController
{
    #[Route(path: '/stations', name: 'api_station_index')]
    public function index(): JsonResponse
    {
        //bundle AcMarche\Issep pas encore porte
        return new JsonResponse(
            ['error' => 'Bundle AcMarche\Issep pas encore porte'],
            Response::HTTP_NOT_IMPLEMENTED,
        );
    }
}

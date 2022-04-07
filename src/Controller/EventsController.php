<?php

namespace AcMarche\Api\Controller;

use AcMarche\Api\Hades\HadesEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/agenda')]
class EventsController extends AbstractController
{
    #[Route(path: '/evenements', name: 'events')]
    public function index(): JsonResponse
    {
        $hadesEvent = new HadesEvent();
        $events = $hadesEvent->getItems();

        return new JsonResponse($events);
    }
}

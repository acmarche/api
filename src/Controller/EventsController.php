<?php

namespace AcMarche\Api\Controller;

use AcMarche\Api\Hades\HadesEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/agenda")
 */
class EventsController extends AbstractController
{
    /**
     * @Route("/evenements", name="events")
     */
    public function index()
    {
        $hadesEvent = new HadesEvent();
        $events = $hadesEvent->getItems();

        return new JsonResponse($events);
    }
}

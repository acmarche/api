<?php

namespace AcMarche\Api\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/agenda')]
class EventsController extends AbstractController
{
    #[Route(path: '/evenements', name: 'events')]
    public function index(): JsonResponse
    {
        $content_json = file_get_contents("https://www.marche.be/api/events.php");
        try {
            $events = json_decode($content_json, null, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $events = [];
        }

        return new JsonResponse($events);
    }
}

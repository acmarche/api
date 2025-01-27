<?php

namespace AcMarche\Api\Controller;

use AcMarche\Api\Entity\Parking;
use AcMarche\Api\Mailer\ApiMailer;
use AcMarche\Api\Parking\EventNotification;
use AcMarche\Api\Parking\Repository\ParkingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Map\Bridge\Leaflet\LeafletOptions;
use Symfony\UX\Map\Bridge\Leaflet\Option\TileLayer;
use Symfony\UX\Map\InfoWindow;
use Symfony\UX\Map\Map;
use Symfony\UX\Map\Marker;
use Symfony\UX\Map\Point;

class ParkingController extends AbstractController
{
    public function __construct(
        private readonly ParkingRepository $parkingRepository,
        private readonly ApiMailer $apiMailer,
    ) {}

    #[Route(path: '/parking', methods: ['POST'])]
    public function parking(Request $request): JsonResponse
    {
        $jsonString = $request->getContent();
        try {
            $eventNotification = new EventNotification($jsonString);
            if (!$parking = $this->parkingRepository->findByNumber($eventNotification->data->id)) {
                $parking = Parking::createFromEvent($eventNotification);
                $this->parkingRepository->insert($parking);
            } else {
                $parking->update($eventNotification);
                $this->parkingRepository->flush();
            }

            return new JsonResponse($parking);
        } catch (\Exception $e) {
            $this->apiMailer->sendError('error:'.$e->getMessage().' => '.$jsonString);

            return new JsonResponse(['error' => 1, 'message' => $e->getMessage(), 'data' => $jsonString],
                Response::HTTP_BAD_REQUEST,
            );
        }
    }

    #[Route(path: '/secure/parking/json', name: 'api_parking_json', methods: ['GET'])]
    #[IsGranted('ROLE_API_API')]
    public function parkingJson(): JsonResponse
    {
        return $this->json($this->parkingRepository->findAll());
    }

    #[Route(path: '/map/parking', name: 'api_parking_map')]
    public function parkingMap(): Response
    {
        $parkings = $this->parkingRepository->findAll();
        $map = (new Map('default'))
            ->center(new Point(50.2292919, 5.34407543,))
            ->zoom(14)
            ->options(
                (new LeafletOptions())
                    ->tileLayer(
                        new TileLayer(
                            url: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                            options: ['maxZoom' => 19],
                        ),
                    ),
            );

        foreach ($parkings as $parking) {
            $map->addMarker(
                new Marker(
                    position: new Point($parking->latitude, $parking->longitude),
                    title: $parking->name,
                    infoWindow: new InfoWindow(content: '<p>'.$parking->name.'</p>'.$parking->status),
                    extra: [
                        'icon_url' => 'https://maps.gstatic.com/mapfiles/place_api/icons/v2/tree_pinlet.svg',
                    ],
                ),
            );
        }

        return $this->render('@AcMarcheApi/parking/index.html.twig', [
            'map' => $map,
            'parkings' => $parkings,
        ]);
    }
}

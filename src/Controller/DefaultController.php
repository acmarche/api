<?php

namespace AcMarche\Api\Controller;

use AcMarche\Api\Entity\Parking;
use AcMarche\Api\Mailer\ApiMailer;
use AcMarche\Api\Parking\EventNotification;
use AcMarche\Api\Parking\Repository\ParkingRepository;
use AcMarche\Icar\Repository\IcarRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\UX\Map\Bridge\Leaflet\LeafletOptions;
use Symfony\UX\Map\Bridge\Leaflet\Option\TileLayer;
use Symfony\UX\Map\InfoWindow;
use Symfony\UX\Map\Map;
use Symfony\UX\Map\Marker;
use Symfony\UX\Map\Point;

class DefaultController extends AbstractController
{
    public function __construct(
        #[Autowire(env: 'BOTTIN_URL')]
        private readonly string $baseUrl,
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
        private readonly IcarRepository $icarRepository,
        private readonly ParkingRepository $parkingRepository,
        private readonly ApiMailer $apiMailer,
        private readonly LoggerInterface $logger,
    ) {}

    #[Route(path: '/', name: 'api_home')]
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheApi/default/index.html.twig',
            [

            ],
        );
    }

    #[Route(path: '/rues', name: 'rues')]
    public function rues(): JsonResponse
    {
        try {
            $rues = $this->icarRepository->findRuesByLocalite(null);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $data = [];
        $i = 0;

        foreach ($rues->rues as $rue) {
            $data[$i]['id'] = $i;
            $data[$i]['nom'] = $rue->nom;
            $data[$i]['code_postal'] = $rue->cps[0];
            $data[$i]['code'] = '';
            $data[$i]['xMin'] = $rue->xMin;
            $data[$i]['xMax'] = $rue->xMax;
            $data[$i]['yMin'] = $rue->yMin;
            $data[$i]['yMax'] = $rue->yMax;
            $data[$i]['localites'] = $rue->localites;
            $i++;
        }

        return new JsonResponse($data);
    }

    #[Route(path: '/fiches/category/{id}', methods: ['GET'], format: 'json')]
    public function cats(int $id): JsonResponse
    {
        return $this->cache->get(
            'cat-'.$id.time(),
            function (ItemInterface $item) use ($id) {
                $item->expiresAfter(18000);
                $url = $this->baseUrl.'/bottin/fiches/category/'.$id;

                return $this->json($this->execute($url));
            },
        );
    }

    #[Route(path: '/thesaurus', methods: ['GET'], format: 'json')]
    public function thesaurus(): JsonResponse
    {
        return $this->cache->get(
            'thesaurus',
            function (ItemInterface $item) {
                $item->expiresAfter(18000);
                $url = $this->baseUrl.'/bottin/categoriestree';

                return $this->json($this->execute($url));
            },
        );
    }

    #[Route(path: '/parking', methods: ['POST'])]
    public function parking(Request $request): JsonResponse
    {
        $jsonString = $request->getContent();
        $this->logger->error("ZEZE ".$jsonString);
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
    #[IsGranted('ROLE_USER')]
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

    private function execute(string $url): array
    {
        $request = $this->httpClient->request("GET", $url);
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if (!$content) {
            return ['error' => 1, 'message' => 'Erreur'];
        }

        return $content;
    }
}

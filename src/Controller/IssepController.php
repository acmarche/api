<?php

namespace AcMarche\Api\Controller;

use AcMarche\Issep\Indice\IndiceUtils;
use AcMarche\Issep\Repository\StationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/issep')]
class IssepController extends AbstractController
{
    public function __construct(
        private readonly StationRepository $stationRepository,
        private readonly IndiceUtils $indiceUtils,
    ) {}

    #[Route(path: '/stations', name: 'api_station_index')]
    public function index(): JsonResponse
    {
        try {
            $stations = $this->stationRepository->getStations();
            $this->indiceUtils->setLastBelAqiOnStations($stations);
        } catch (\Exception $e) {
            $stations = [];
            $this->addFlash('danger', $e->getMessage());
        }

        try {
            $this->indiceUtils->setLastData($stations);
        } catch (\DateMalformedStringException $e) {
        }

        return $this->json($stations);
    }


}

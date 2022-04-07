<?php

namespace AcMarche\Api\Controller;

use AcMarche\Api\Logger\LoggerDb;
use AcMarche\Api\Repository\RueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DefaultController extends AbstractController
{

    public function __construct(
        private HttpClientInterface $httpClient,
        private CacheInterface $cache,
        private RueRepository $rueRepository,
        private string $baseUrl
    ) {
    }

    #[Route(path: '/', name: 'api_home')]
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheApi/default/index.html.twig',
            [

            ]
        );
    }


    #[Route(path: '/rues', name: 'rues')]
    public function rues(): JsonResponse
    {
        $rues = $this->rueRepository->findAll();
        $data = [];
        $i = 0;
        foreach ($rues as $rue) {
            $data[$i]['id'] = $rue->getId();
            $data[$i]['code'] = $rue->getCode();
            $data[$i]['nom'] = $rue->getNom();
            $data[$i]['localite'] = $rue->getLocalite();
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
            }
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
            }
        );
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

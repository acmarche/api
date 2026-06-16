<?php

namespace AcMarche\Api\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BottinController extends AbstractController
{
    private string $cache_prefix = 'api_cache44';

    public function __construct(
        #[Autowire(env: 'BOTTIN_URL')]
        private readonly string $baseUrl,
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
    ) {
    }

    #[Route(path: '/bottin/fiches', name: 'bottin_api_fiches', methods: ['GET'], format: 'json')]
    public function fiches(): JsonResponse
    {
        return $this->cache->get(
            'allfiches'.$this->cache_prefix,
            function (ItemInterface $item) {
                $item->expiresAfter(18000);
                $url = $this->baseUrl.'/bottin/fiches';

                return $this->json($this->execute($url));
            },
        );
    }

    #[Route(path: '/bottin/category/{id}', methods: ['GET'], format: 'json')]
    public function category(int $id): JsonResponse
    {
        return $this->cache->get(
            'category5-'.$id.'-'.$this->cache_prefix,
            function (ItemInterface $item) use ($id) {
                $item->expiresAfter(18000);
                $url = $this->baseUrl.'/bottin/category/'.$id;

                return $this->json($this->execute($url));
            },
        );
    }

    #[Route(path: '/bottin/fiches/rubrique/{id}', name: 'bottin_api_fiche_by_category', methods: ['GET'], format: 'json')]
    public function ficheByCategory($id): JsonResponse
    {
        return $this->cache->get(
            'fiche-by-category-'.$id.$this->cache_prefix,
            function (ItemInterface $item) use ($id) {
                $item->expiresAfter(10000);
                $url = $this->baseUrl.'/bottin/fiches/category/'.$id;

                $dataTmp = $this->execute($url);

                if (isset($dataTmp['error'])) {
                    if ($dataTmp['error'] == 1) {
                        return $this->json($dataTmp);
                    }
                }
                $data = [];
                foreach ($dataTmp as $fiche) {
                    $fiche['cap'] = [];
                    $data[] = $fiche;
                }

                return $this->json($data);
            },
        );
    }

    #[Route(path: '/bottin/fiche/{id}', name: 'bottin_api_fiche_id', methods: ['GET'], format: 'json')]
    public function ficheById(int|string $id, Request $request): JsonResponse
    {
        $authorization = $request->headers->get('Authorization');

        // When a bearer token is provided, bypass the cache and forward it to
        // bottin.marche.be so the protected "token" field can be returned. The
        // response is not cached to avoid leaking the token to anonymous callers.
        if ($authorization !== null && $authorization !== '') {
            return $this->json($this->fetchFicheById($id, $authorization));
        }

        return $this->cache->get(
            'fiche-byid-'.$id.$this->cache_prefix,
            function (ItemInterface $item) use ($id) {
                $item->expiresAfter(18000);

                return $this->json($this->fetchFicheById($id, null));
            },
        );
    }

    private function fetchFicheById(int|string $id, ?string $authorization): ?array
    {
        $url = $this->baseUrl.'/bottin/fiche/'.$id;

        try {
            $fiche = $this->execute($url, $authorization);
        } catch (\Exception $exception) {
            return null;
        }

        if ($fiche === null || isset($fiche['error'])) {
            return null;
        }

        $fiche['cap'] = [];

        return $fiche;
    }

    #[Route(path: '/bottin/fichebyslugname/{slug}', name: 'bottin_api_fiche_slug', methods: ['GET'], format: 'json')]
    public function ficheSlug(string $slug,  Request $request): JsonResponse
    {
        $slug = preg_replace("#\.#", "", $slug);
        $url = $this->baseUrl.'/bottin/fichebyslugname/'.$slug;
        $authorization = $request->headers->get('Authorization');

        try {
            $fiche = $this->execute($url, $authorization);
        } catch (\Exception $exception) {
            return $this->json(['error'=>$exception->getMessage()]);
        }

        if (!$fiche) {
            return $this->json(null);
        }

        if (isset($fiche['error'])) {
            return $this->json($fiche);
        }

        $fiche['cap'] = [];

        return $this->json($fiche);
    }

    private function execute(string $url, ?string $authorization = null): ?array
    {
        $options = [];
        if ($authorization !== null && $authorization !== '') {
            $options['headers'] = ['Authorization' => $authorization];
        }
        try {
            $request = $this->httpClient->request("GET", $url, $options);
        } catch (TransportExceptionInterface $e) {
            return ['error' => 1, 'message' => 'Error '.$e->getMessage()];
        }
        try {
            if ($request->getStatusCode() === 404) {
                return ['error' => 1, 'message' => 'Not found'];
            }
        } catch (TransportExceptionInterface $e) {
            return ['error' => 1, 'message' => 'Error '.$e->getMessage()];
        }
        try {
            return json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException|TransportExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface|ClientExceptionInterface $e) {
            return ['error' => 1, 'message' => 'Error '.$e->getMessage()];
        }
    }

}

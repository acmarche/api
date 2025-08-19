<?php

namespace AcMarche\Api\Controller;

use AcMarche\Api\Http\CapApi;
use AcMarche\Api\Logger\LoggerDb;
use AcMarche\Api\Mailer\ApiMailer;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    private string $cache_prefix = 'api_cache22';

    public function __construct(
        #[Autowire(env: 'BOTTIN_URL')]
        private readonly string $baseUrl,
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
        private readonly LoggerDb $loggerDb,
        private readonly LoggerInterface $logger,
        private readonly ApiMailer $mailer,
        private readonly CapApi $capApi,
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

    #[Route(path: '/bottin/fichesandroid', name: 'bottin_api_fiches_all', methods: ['GET'], format: 'json')]
    public function fichesAll(): JsonResponse
    {
        return $this->cache->get(
            'allfichesandroid'.$this->cache_prefix,
            function (ItemInterface $item) {
                $item->expiresAfter(18000);
                $url = $this->baseUrl.'/bottin/fichesandroid';

                return $this->json($this->execute($url));
            },
        );
    }

    #[Route(path: '/bottin/commerces', name: 'bottin_api_commerces', methods: ['GET'], format: 'json')]
    public function commerces(): JsonResponse
    {
        return $this->cache->get(
            'commerces'.$this->cache_prefix,
            function (ItemInterface $item) {
                $item->expiresAfter(18000);
                $url = $this->baseUrl.'/bottin/commerces/';

                return $this->json($this->execute($url));
            },
        );
    }

    #[Route(path: '/bottin/category-by-slug/{slug}', methods: ['GET'], format: 'json')]
    public function categoryBySlug(string $slug): JsonResponse
    {
        return $this->cache->get(
            'category-'.$slug.'-'.$this->cache_prefix,
            function (ItemInterface $item) use ($slug) {
                $item->expiresAfter(18000);
                $url = $this->baseUrl.'/bottin/category-by-slug/'.$slug;

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
                    $cap = null;
                    try {
                        $cap = json_decode($this->capApi->find($fiche['id']));
                    } catch (\Exception $exception) {
                        $this->mailer->sendError($exception->getMessage());
                    }

                    $capFiche = [];
                    if ($cap && $cap->commercantId) {
                        try {
                            $capFiche = json_decode($this->capApi->shop($cap->commercantId));
                        } catch (\Exception $exception) {
                            $this->mailer->sendError($exception->getMessage());
                        }
                    }

                    if (isset($capFiche->rightAccess)) {
                        unset($capFiche->rightAccess);
                    }

                    $fiche['cap'] = $capFiche;
                    $data[] = $fiche;
                }

                return $this->json($data);
            },
        );
    }

    #[Route(path: '/bottin/fiches/cap/rubrique/{id}', methods: ['GET'], format: 'json')]
    public function fichesCap($id): JsonResponse
    {
        return $this->cache->get(
            'fiche-cap-by-category-'.$id.$this->cache_prefix,
            function (ItemInterface $item) use ($id) {
                $item->expiresAfter(10000);

                $url = $this->baseUrl.'/bottin/fiches';
                if ((int)$id > 0) {
                    $url = $this->baseUrl.'/bottin/fiches/category/'.$id;
                }

                $dataTmp = $this->execute($url);

                if (isset($dataTmp['error'])) {
                    if ($dataTmp['error'] == 1) {
                        return $this->json($dataTmp);
                    }
                }
                $data = [];
                $i = 0;
                foreach ($dataTmp as $fiche) {
                    if ($i > 50) {
                        break;
                    }
                    $cap = null;
                    try {
                        $cap = json_decode($this->capApi->find($fiche['id']));
                    } catch (\Exception $exception) {
                        //$this->mailer->sendError($exception->getMessage());
                    }

                    $capFiche = [];
                    if ($cap && $cap->commercantId) {
                        try {
                            $capFiche = json_decode($this->capApi->shop($cap->commercantId));
                        } catch (\Exception $exception) {
                            //  $this->mailer->sendError($exception->getMessage());
                        }
                    }

                    if (isset($capFiche->rightAccess)) {
                        unset($capFiche->rightAccess);
                    }
                    $fiche['cap'] = $capFiche;
                    $data[] = $fiche;
                    $i++;
                }

                return $this->json($data);
            },
        );
    }

    #[Route(path: '/bottin/search/cap/rubrique/{id}/{noon}/{sunday}', defaults: [
        'noon' => false,
        'sunday' => false,
    ], methods: ['GET'], format: 'json')]
    public function searchfichesCap(int $id, $noon, $sunday): JsonResponse
    {
        //si false dans url met true :-(
        if ($noon == "false") {
            $noon = 0;
        } else {
            $noon = 1;
        }
        if ($sunday == "false") {
            $sunday = 0;
        } else {
            $sunday = 1;
        }

        return $this->cache->get(
            'fiche-cap-by-category-'.$id.$noon.$sunday.$this->cache_prefix,
            function (ItemInterface $item) use ($id, $noon, $sunday) {
                $item->expiresAfter(10000);

                $url = $this->baseUrl.'/bottin/fiches';
                if ($id > 0) {
                    $url = $this->baseUrl.'/bottin/cap/search/'.$id.'/'.$noon.'/'.$sunday;
                }

                $dataTmp = $this->execute($url);

                if (isset($dataTmp['error'])) {
                    if ($dataTmp['error'] == 1) {
                        return $this->json($dataTmp);
                    }
                }
                $data = [];
                $i = 0;
                foreach ($dataTmp as $fiche) {
                    if ($i > 50) {
                        break;
                    }
                    $cap = null;
                    try {
                        $cap = json_decode($this->capApi->find($fiche['id']));
                    } catch (\Exception $exception) {
                        //$this->mailer->sendError($exception->getMessage());
                    }

                    $capFiche = [];
                    if ($cap && $cap->commercantId) {
                        try {
                            $capFiche = json_decode($this->capApi->shop($cap->commercantId));
                        } catch (\Exception $exception) {
                            //  $this->mailer->sendError($exception->getMessage());
                        }
                    }

                    if (isset($capFiche->rightAccess)) {
                        unset($capFiche->rightAccess);
                    }
                    $fiche['cap'] = $capFiche;
                    $data[] = $fiche;
                    $i++;
                }

                return $this->json($data);
            },
        );
    }

    #[Route(path: '/bottin/fiches/cap/rubrique-by-slug/{slug}', methods: ['GET'], format: 'json')]
    public function fichesCapSlug(?string $slug): JsonResponse
    {
        return $this->cache->get(
            'fiche-cap-by-category-'.$slug.$this->cache_prefix,
            function (ItemInterface $item) use ($slug) {
                $item->expiresAfter(10000);

                $url = $this->baseUrl.'/bottin/fiches';
                if ($slug) {
                    $url = $this->baseUrl.'/bottin/fiches/category-by-slug/'.$slug;
                }

                $dataTmp = $this->execute($url);

                if (isset($dataTmp['error'])) {
                    if ($dataTmp['error'] == 1) {
                        return $this->json($dataTmp);
                    }
                }
                $data = [];
                $i = 0;
                foreach ($dataTmp as $fiche) {
                    if ($i > 50) {
                        break;
                    }
                    $cap = null;
                    try {
                        $cap = json_decode($this->capApi->find($fiche['id']));
                    } catch (\Exception $exception) {
                        //$this->mailer->sendError($exception->getMessage());
                    }

                    $capFiche = [];
                    if ($cap && $cap->commercantId) {
                        try {
                            $capFiche = json_decode($this->capApi->shop($cap->commercantId));
                        } catch (\Exception $exception) {
                            //  $this->mailer->sendError($exception->getMessage());
                        }
                    }

                    if (isset($capFiche->rightAccess)) {
                        unset($capFiche->rightAccess);
                    }
                    $fiche['cap'] = $capFiche;
                    $data[] = $fiche;
                    $i++;
                }

                return $this->json($data);
            },
        );
    }

    /**
     * Enfance jeunesse
     */
    #[Route(path: '/bottin/nocache/fiches/rubrique/{id}', name: 'bottin_api_fiche_by_category_nocache', methods: ['GET'], format: 'json')]
    public function ficheByCategoryNoCache($id): JsonResponse
    {
        $url = $this->baseUrl.'/bottin/fiches/category/'.$id;

        return $this->json($this->execute($url));
    }

    #[Route(path: '/bottin/fiche/{id}', name: 'bottin_api_fiche_id', methods: ['GET'], format: 'json')]
    public function ficheById(int|string $id): JsonResponse
    {
        return $this->cache->get(
            'fiche-byid-'.$id.$this->cache_prefix,
            function (ItemInterface $item) use ($id) {
                $item->expiresAfter(18000);
                $url = $this->baseUrl.'/bottin/fichebyid/'.$id;

                try {
                    $fiche = $this->execute($url);
                } catch (\Exception $exception) {
                    return $this->json(null);
                }

                if (isset($fiche['error'])) {
                    return $this->json(null);
                }

                $cap = json_decode($this->capApi->find($fiche['id']));
                $capFiche = [];
                if ($cap && $cap->commercantId) {
                    try {
                        $capFiche = json_decode($this->capApi->shop($cap->commercantId));
                    } catch (\Exception $exception) {
                    }
                }

                if (isset($capFiche->rightAccess)) {
                    unset($capFiche->rightAccess);
                }
                $fiche['cap'] = $capFiche;

                return $this->json($fiche);
            },
        );
    }

    #[Route(path: '/bottin/fichebyslugname/{slug}', name: 'bottin_api_fiche_slug', methods: ['GET'], format: 'json')]
    public function ficheSlug(string $slug): JsonResponse
    {
        $slug = preg_replace("#\.#", "", $slug);
        $url = $this->baseUrl.'/bottin/fichebyslugname/'.$slug;

        $fiche = $this->execute($url);
        if (!$fiche) {
            return $this->json(null);
        }

        if (isset($fiche['error'])) {
            return $this->json(null);
        }

        $capFiche = [];
        $cap = json_decode($this->capApi->find($fiche['id']));
        if ($cap && $cap->commercantId) {
            try {
                $capFiche = json_decode($this->capApi->shop($cap->commercantId));
            } catch (\Exception $exception) {
            }
        }

        if (isset($capFiche->rightAccess)) {
            unset($capFiche->rightAccess);
        }
        $fiche['cap'] = $capFiche;

        return $this->json($fiche);
    }

    #[Route(path: '/bottin/fichebyids', name: 'bottin_api_fiche_ids', methods: ['POST'], format: 'json')]
    public function ficheByIds(Request $request): Response
    {
        $ids = json_decode($request->request->get('ids'), true, 512, JSON_THROW_ON_ERROR);
        if (!$ids) {
            return new JsonResponse(['error' => 1, 'message' => 'Paramète ids obligatoire']);
        }
        if (!is_array($ids)) {
            return new JsonResponse(['error' => 1, 'message' => 'Format json invalide']);
        }
        if (count($ids) < 1) {
            return new JsonResponse(['error' => 1, 'message' => 'Au moins un id est nécessaire']);
        }
        $url = $this->baseUrl.'/bottin/fichebyids';
        $fields = ['ids' => json_encode($ids, JSON_THROW_ON_ERROR)];
        $request = $this->httpClient->request(
            "POST",
            $url,
            [
                'body' => $fields,
            ],
        );

        return new Response($request->getContent());
    }

    #[Route(path: '/search/bottin/fiches/_search', name: 'bottin_api_search', methods: ['POST'], format: 'json')]
    public function search(Request $request): Response
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $query = $content['query'];
        $bool = $query['bool'];
        $should = $bool['should'];
        $societe = $should['0']['match']['societe_autocomplete'];
        $keyword = preg_replace("#'#", " ", $societe);
        $url = $this->baseUrl.'/bottin/search';
        $request = $this->httpClient->request(
            "POST",
            $url,
            [
                'body' => ['keyword' => $keyword],
            ],
        );
        $content = $request->getContent();
        $this->loggerDb->logSearch($keyword);

        return new Response($content);
    }

    #[Route(path: '/admin/updatefiche', name: 'bottin_api_update_fiche', methods: ['POST'], format: 'json')]
    public function updatefiche(Request $request): JsonResponse
    {
        $fields = (array)json_decode($request->getContent(), null, 512, JSON_THROW_ON_ERROR);
        $url = $this->baseUrl.'/updatefiche';
        if (!isset($fields['id'])) {
            $this->logger->critical('##api## error api update fiche id manquant ');

            return new JsonResponse(['error' => 'id manquant']);
        }
        try {
            $request = $this->httpClient->request(
                "POST",
                $url,
                [
                    'body' => $fields,
                ],
            );
        } catch (TransportExceptionInterface $e) {
            $this->logger->critical('##api## error api update fiche '.$e->getMessage());

            return $this->json(['error' => $e->getMessage()]);
        }
        $content = $request->getContent();
        $this->logger->info('##api## update fiche '.$content);

        return new JsonResponse($content);
    }

    #[Route(path: '/bottin/classements', name: 'bottin_api_classements', methods: ['GET'], format: 'json')]
    public function classements(): JsonResponse
    {
        return $this->cache->get(
            'classements'.$this->cache_prefix,
            function (ItemInterface $item) {
                $item->expiresAfter(18000);
                $url = $this->baseUrl.'/bottin/classements';

                return $this->json($this->execute($url));
            },
        );
    }

    #[Route(path: '/bottin/categories', name: 'bottin_api_categories', methods: ['GET'], format: 'json')]
    public function categories(): JsonResponse
    {
        return $this->cache->get(
            'categories'.$this->cache_prefix,
            function (ItemInterface $item) {
                $item->expiresAfter(18000);
                $url = $this->baseUrl.'/bottin/categories';

                return $this->json($this->execute($url));
            },
        );
    }

    #[Route(path: '/bottin/categories/byparent/{id}', name: 'bottin_api_categories_by_parent', methods: ['GET'], format: 'json')]
    public function categoriesByParent(int $id): JsonResponse
    {
        if ($id > 0) {
            return $this->cache->get(
                'categories_by_parent_'.$id.$this->cache_prefix,
                function (ItemInterface $item) use ($id) {
                    $item->expiresAfter(18000);
                    $url = $this->baseUrl.'/bottin/categories/parent/'.$id;

                    return $this->json($this->execute($url));
                },
            );
        }

        return $this->json([]);
    }

    private function execute(string $url): ?array
    {
        try {
            $request = $this->httpClient->request("GET", $url);
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

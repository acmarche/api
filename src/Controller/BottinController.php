<?php

namespace AcMarche\Api\Controller;

use AcMarche\Api\Logger\LoggerDb;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Old api pour cap
 * Class BottinController
 * @package AcMarche\Api\Controller
 *
 */
class BottinController extends AbstractController
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;
    /**
     * @var string
     */
    private $baseUrl;
    /**
     * @var LoggerDb
     */
    private $loggerDb;
    /**
     * @var FilesystemAdapter
     */
    private $cache;

    public function __construct(
        HttpClientInterface $httpClient,
        CacheInterface $cache,
        LoggerDb $loggerDb,
        string $baseUrl
    ) {
        $this->httpClient = $httpClient;
        $this->baseUrl = $baseUrl;
        $this->loggerDb = $loggerDb;
        $this->cache = $cache;
    }

    /**
     * @Route("/bottin/fiches", name="bottin_api_fiches", methods={"GET"}, format="json")
     */
    public function fiches(): JsonResponse
    {
        $value = $this->cache->get(
            'allfiches',
            function (ItemInterface $item) {
                $item->expiresAfter(18000);
                $url = $this->baseUrl.'/bottin/fiches';

                return $this->json($this->execute($url));
            }
        );

        return $value;
    }

    /**
     * @Route("/bottin/fichesandroid", name="bottin_api_fiches_all", methods={"GET"}, format="json")
     */
    public function fichesAll(): JsonResponse
    {
        $value = $this->cache->get(
            'allfichesandroid',
            function (ItemInterface $item) {
                $item->expiresAfter(18000);
                $url = $this->baseUrl.'/bottin/fichesandroid';

                return $this->json($this->execute($url));
            }
        );

        return $value;
    }

    /**
     * @Route("/bottin/commerces", name="bottin_api_commerces", methods={"GET"}, format="json")
     */
    public function commerces(): JsonResponse
    {
        $value = $this->cache->get(
            'commerces',
            function (ItemInterface $item) {
                $item->expiresAfter(18000);
                $url = $this->baseUrl.'/bottin/commerces/';

                return $this->json($this->execute($url));
            }
        );

        return $value;
    }

    /**
     * @Route("/bottin/fiches/rubrique/{id}", name="bottin_api_fiche_by_category", methods={"GET"}, format="json")
     */
    public function ficheByCategory($id): JsonResponse
    {
        $value = $this->cache->get(
            'fichebycategory-'.$id,
            function (ItemInterface $item) use ($id) {
                $item->expiresAfter(18000);
                $url = $this->baseUrl.'/bottin/fiches/category/'.$id;

                return $this->json($this->execute($url));
            }
        );

        return $value;
    }

    /**
     * Enfance jeunesse
     * @Route("/bottin/nocache/fiches/rubrique/{id}", name="bottin_api_fiche_by_category_nocache", methods={"GET"}, format="json")
     */
    public function ficheByCategoryNoCache($id): JsonResponse
    {
        $url = $this->baseUrl.'/bottin/fiches/category/'.$id;

        return $this->json($this->execute($url));
    }

    /**
     * @Route("/bottin/fiche/{id}", name="bottin_api_fiche_id", methods={"GET"}, format="json")
     */
    public function ficheById(int $id): JsonResponse
    {
        $url = $this->baseUrl.'/bottin/fichebyid/'.$id;

        return $this->json($this->execute($url));
    }

    /**
     * @Route("/bottin/fichebyslugname/{slug}", name="bottin_api_fiche_slug", methods={"GET"}, format="json")
     */
    public function ficheSlug(string $slug): JsonResponse
    {
        $slug = preg_replace("#\.#", "", $slug);
        $url = $this->baseUrl.'/bottin/fichebyslugname/'.$slug;

        return $this->json($this->execute($url));
    }

    /**
     * @Route("/bottin/fichebyids", name="bottin_api_fiche_ids", methods={"POST"}, format="json")
     */
    public function ficheByIds(Request $request): Response
    {
        $ids = json_decode($request->request->get('ids'), true);

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
        $fields = ['ids' => json_encode($ids)];
        $request = $this->httpClient->request(
            "POST",
            $url,
            [
                'body' => $fields,
            ]
        );

        return new Response($request->getContent());
    }

    /**
     * @Route("/search/bottin/fiches/_search", name="bottin_api_search", methods={"POST"}, format="json")
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): Response
    {
        $content = json_decode($request->getContent(), true);
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
            ]
        );

        $content = $request->getContent();
        $this->loggerDb->logSearch($keyword);

        return new Response($content);
    }

    /**
     *
     * @Route("/admin/updatefiche", name="bottin_api_update_fiche", methods={"POST"}, format="json")
     */
    public function updatefiche(Request $request): JsonResponse
    {
        $fields = $request->request->all();
        $url = $this->baseUrl.'/updatefiche';
        $request = $this->httpClient->request(
            "POST",
            $url,
            [
                'body' => $fields,
            ]
        );

        return new JsonResponse($request->getContent());
    }

    /**
     * @Route("/bottin/classements", name="bottin_api_classements", methods={"GET"}, format="json")
     */
    public function classements(): JsonResponse
    {
        $value = $this->cache->get(
            'classements',
            function (ItemInterface $item) {
                $item->expiresAfter(18000);
                $url = $this->baseUrl.'/bottin/classements';

                return $this->json($this->execute($url));
            }
        );

        return $value;
    }

    /**
     * @Route("/bottin/categories", name="bottin_api_categories", methods={"GET"}, format="json")
     */
    public function categories(): JsonResponse
    {
        $value = $this->cache->get(
            'categories',
            function (ItemInterface $item) {
                $item->expiresAfter(18000);
                $url = $this->baseUrl.'/bottin/categories';

                return $this->json($this->execute($url));
            }
        );

        return $value;
    }

    private function execute(string $url): array
    {
        $request = $this->httpClient->request("GET", $url);
        $content = json_decode($request->getContent(), true);
        if (!$content) {
            return ['error' => 1, 'message' => 'Erreur'];
        }

        return $content;
    }

}

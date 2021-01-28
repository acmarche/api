<?php

namespace AcMarche\Api\Controller;

use AcMarche\Api\Logger\LoggerDb;
use AcMarche\Api\Repository\RueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DefaultController extends AbstractController
{

    /**
     * @var HttpClientInterface
     */
    private $httpClient;
    /**
     * @var CacheInterface
     */
    private $cache;
    /**
     * @var LoggerDb
     */
    private $loggerDb;
    /**
     * @var RueRepository
     */
    private $rueRepository;
    /**
     * @var string
     */
    private $baseUrl;

    public function __construct(
        HttpClientInterface $httpClient,
        CacheInterface $cache,
        LoggerDb $loggerDb,
        RueRepository $rueRepository,
        string $baseUrl
    )
    {
        $this->httpClient = $httpClient;
        $this->cache = $cache;
        $this->loggerDb = $loggerDb;
        $this->rueRepository = $rueRepository;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @Route("/", name="default", name="api_home")
     */
    public function index()
    {
        return $this->render(
            '@AcMarcheApi/default/index.html.twig',
            [

            ]
        );
    }


    /**
     * @Route("/rues", name="rues")
     */
    public function rues()
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

    /**
     * @Route("/fiches/category/{id}", methods={"GET"}, format="json")
     */
    public function cats(int $id): JsonResponse
    {
        $value = $this->cache->get(
            'cat-' . $id . time(),
            function (ItemInterface $item) use ($id) {
                $item->expiresAfter(18000);
                $url = $this->baseUrl . '/bottin/fiches/category/' . $id;

                return $this->json($this->execute($url));
            }
        );

        return $value;
    }

    /**
     * @Route("/thesaurus", methods={"GET"}, format="json")
     */
    public function thesaurus(): JsonResponse
    {
        $value = $this->cache->get(
            'thesaurus',
            function (ItemInterface $item) {
                $item->expiresAfter(18000);
                $url = $this->baseUrl . '/bottin/categoriestree';

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

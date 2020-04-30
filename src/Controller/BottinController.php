<?php

namespace AcMarche\Api\Controller;

use AcMarche\Api\Logger\LoggerDb;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Old api pour cap
 * Class BottinController
 * @package AcMarche\Api\Controller
 *
 */
class BottinController extends AbstractController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

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

    public function __construct(HttpClientInterface $httpClient, LoggerDb $loggerDb, string $baseUrl)
    {
        $this->httpClient = $httpClient;
        $this->baseUrl = $baseUrl;
        $this->loggerDb = $loggerDb;
    }

    /**
     * @Route("/bottin/fiches", name="bottin_api_fiches", methods={"GET"}, format="json")
     */
    public function fiches(): JsonResponse
    {
        $url = $this->baseUrl.'/bottin/fiches';

        return $this->json($this->execute($url));
    }

    /**
     * @Route("/bottin/commerces", name="bottin_api_commerces", methods={"GET"}, format="json")
     */
    public function commerces(): JsonResponse
    {
        $url = $this->baseUrl.'/bottin/commerces/';

        return $this->json($this->execute($url));
    }

    /**
     * @Route("/bottin/fiches/rubrique/{id}", name="bottin_api_fiche_by_category", methods={"GET"}, format="json")
     */
    public function ficheByCategory($id): JsonResponse
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
        $url = $this->baseUrl.'/bottin/fichebyslugname/'.$slug;

        return $this->json($this->execute($url));
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
        $keyword = preg_replace("#'#", "", $societe);

        $url = $this->baseUrl.'/bottin/search';
        $request = $this->httpClient->request(
            "POST",
            $url,
            [
                'body' => ['keyword' => $keyword],
            ]
        );

        $content = $request->getContent();
        //$this->logger->error(json_encode($content), ['api_search']);
        $this->logger->error($keyword, ['api_search']);
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

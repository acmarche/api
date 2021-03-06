<?php


namespace AcMarche\Api\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class TestController
 * @package AcMarche\Api\Controller
 * @IsGranted("ROLE_ADMIN")
 */
class TestController extends AbstractController
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;
    /**
     * @var string
     */
    private $baseUrl;

    public function __construct(HttpClientInterface $httpClient, string $baseUrl)
    {
        $this->httpClient = $httpClient;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @Route("/bottin/test/ids", name="bottin_test_ids")
     *
     */
    public function testIDs(): Response
    {
        $ids = json_encode([393, 522, 55]);
        $fields = ['ids' => $ids];

        $url = $this->baseUrl.'/bottin/fichebyids';

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
     * @Route("/bottin/test/updatefiche", name="bottin_test_updatefiche")
     *
     */
    public function testPostFiche(): Response
    {
        $fields = array('id' => 393, 'fax' => '084 12 34 56', 'gsm' => '0476 12 34 56');

        $url = $this->baseUrl.'/updatefiche';
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
     * @Route("/bottin/test/search", name="bottin_test_search")
     *
     */
    public function testSearch(): Response
    {
        $data = ['keyword' => 'AXA'];

        $url = $this->baseUrl.'/bottin/search';
        $request = $this->httpClient->request(
            "POST",
            $url,
            [
                'body' => $data,
            ]
        );

        return new Response($request->getContent());
    }

}

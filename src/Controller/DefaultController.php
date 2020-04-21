<?php

namespace AcMarche\Api\Controller;

use AcMarche\Api\Repository\RueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @var RueRepository
     */
    private $rueRepository;

    public function __construct(RueRepository $rueRepository)
    {
        $this->rueRepository = $rueRepository;
    }

    /**
     * @Route("/", name="default")
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
}

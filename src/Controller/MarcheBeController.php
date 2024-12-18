<?php

namespace AcMarche\Api\Controller;

use AcMarche\Api\Necrologie\Necrologie;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/')]
class MarcheBeController extends AbstractController
{
    public function __construct(private readonly Necrologie $necrologie)
    {
    }

    #[Route(path: '/actualites', name: 'actualite')]
    public function index(): JsonResponse
    {
        $content_json = file_get_contents("https://www.marche.be/api/actus.php");
        try {
            $actus = json_decode($content_json, null, 512, JSON_THROW_ON_ERROR);
        }
        catch (\Exception $exception){
             return new JsonResponse([]);
        }
        $new = array();
        $i = 0;
        foreach ($actus as $post) {
            $title = $post->post_title;
            $guid = $post->guid;
            $post_excerpt = $post->post_excerpt;
            $content = $post->post_content;
            $post_date = new DateTime($post->post_date);
            $date_english = $post_date->format('Y-m-d');
            $post_thumbnail = $post->post_thumbnail_url;

            if (!isset($post->image)) {
                $image = $post_thumbnail;
            } else {
                $image = $post->image;
            }

            $new[$i]["intitule"] = $title;
            $new[$i]["extrait"] = $post_excerpt;
            $new[$i]["content"] = $content;
            $new[$i]["url"] = $guid;
            $new[$i]["date"] = $date_english;
            $new[$i]["id"] = $post->ID;
            $new[$i]["image"] = $image;
            $new[$i]["thumbnail"] = $image;
            $i++;
        }

        return new JsonResponse($new);
    }

    #[Route(path: '/marchebe/necrologie/', name: 'necrologie')]
    #[Route(path: '/marchebe/necrologie/{fullpage}', name: 'necrologie_full')]
    public function getNecrologie(bool $fullpage = false): Response
    {
        return new Response($this->necrologie->getNecro($fullpage));
    }
}

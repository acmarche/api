<?php

namespace AcMarche\Api\Controller;

use AcMarche\Api\Necrologie\Necrologie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/marchebe")
 */
class MarcheBeController extends AbstractController
{
    /**
     * @var Necrologie
     */
    private $necrologie;

    public function __construct(Necrologie $necrologie)
    {
        $this->necrologie = $necrologie;
    }

    /**
     * @Route("/actualites", name="actualite")
     */
    public function index()
    {
        $content_json = file_get_contents("https://www.marche.be/api/actus.php");
        $actus = json_decode($content_json);

        $new = array();
        $i = 0;

        foreach ($actus as $post) {
            // var_dump($post);
            $title = $post->post_title;
            //echo $title.'<br />';
            $id = $post->ID;
            $guid = $post->guid;
            $post_excerpt = $post->post_excerpt;
            $content = $post->post_content;
            $post_date = new \DateTime($post->post_date);
            $date_english = $post_date->format('Y-m-d');
            $post_thumbnail = $post->post_thumbnail_url;

            $permalink = $post->permalink;
            $new[$i]["intitule"] = $title;
            $new[$i]["extrait"] = $post_excerpt;
            $new[$i]["content"] = $content;
            $new[$i]["url"] = $guid;
            $new[$i]["date"] = $date_english;
            $new[$i]["id"] = $post->ID;
            $new[$i]["image"] = $post->image;
            $new[$i]["thumbnail"] = $post_thumbnail;
            $i++;
        }

        return new JsonResponse($new);
    }

    /**
     * @Route("/necrologie/", name="necrologie")
     * @Route("/necrologie/{fullpage}", name="necrologie_full")
     * @return string
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getNecrologie(bool $fullpage = false): Response
    {
        return new Response($this->necrologie->getNecro($fullpage));
    }
}

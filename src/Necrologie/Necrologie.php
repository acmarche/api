<?php


namespace AcMarche\Api\Necrologie;

use Psr\Cache\CacheItemPoolInterface;
use SoapClient;
use Twig\Environment;

class Necrologie
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;
    /**
     * @var Environment
     */
    private $environment;

    public function __construct(CacheItemPoolInterface $cache, Environment $environment)
    {
        $this->cache = $cache;
        $this->environment = $environment;
    }

    public function getNecro(bool $fullpage = false)
    {
        $necrologie = $this->cache->getItem('necrologie_' . $fullpage);

        if (!$necrologie->isHit()) {
            $pensees = $this->getDansNosPensees();
            $enaos = $this->getEnaos();

            if ($fullpage) {
                $html = $this->environment->render(
                    '@AcMarcheApi/marchebe/deces/necrologie.html.twig',
                    [
                        'pensees' => $pensees,
                        'enaos' => $enaos,
                    ]
                );
            } else {
                $html = $this->environment->render(
                    '@AcMarcheApi/marchebe/deces/_content.html.twig',
                    [
                        'pensees' => $pensees,
                        'enaos' => $enaos,
                    ]
                );
            }
            $necrologie->expiresAt(new \DateTime('tomorrow'));
            $this->cache->save($necrologie->set($html));
        }

        return $necrologie->get();
    }

    private function getDansNosPensees()
    {
        $url = "http://www.dansnospensees.be/rss.svc/fr/rss/recent/6900";
        $flux = file_get_contents($url);
        if (!$flux) {
            return '<h3 class="text-danger">La source n\'a pas pu être lue</h3>';
        }

        $xml = new \SimpleXmlElement($flux);
        $channel = $xml->channel;

        return $this->environment->render(
            '@AcMarcheApi/marchebe/deces/_pensee.html.twig',
            [
                'title' => $channel,
                'items' => $channel->item,
            ]
        );
    }

    private function getEnaos()
    {
        $url = "http://webservices.enaos.net/derniersdeces.asmx?WSDL";

        $service = new SoapClient(
            $url, array(
                    'soap_version' => SOAP_1_2,
                    'trace' => true,
                    'connection_timeout' => 4,
                    'exceptions' => true,
                )
        );

        $page = 1;

        /* if (isset($_GET['Page']))
          $page = $_GET['Page']; */

        $params = array(
            'Interlocuteur' => 22,
            'MDP' => '2013-wmb',
            'Pays' => 23,
            'CP' => 6900,
            'Page' => $page,
            'NbreParPage' => 30,
        );

        $titre = '<h3 id="enaos">Source Enaos :</h3>';
        $result = $service->ParCodePostalEnHTMLAvecCtrl($params);
        $content = preg_replace("|#URL-DERNIERS-DECES#|", "", $result->ParCodePostalEnHTMLAvecCtrlResult);
        //http://www.enaos.net/P1220.aspx?IdPer=285754
        ////#URL-PERSONNE#?Personne=
        $content = preg_replace("|#URL-PERSONNE#\?Personne|", "http://www.enaos.net/P1220.aspx?IdPer", $content);
        $content = preg_replace("#Derniers décès#", "", $content);
        $content = preg_replace("#Page précédente#", "", $content);
        $content = preg_replace("#Page suivante#", "", $content);
        $content = preg_replace(
            "#ENAOS.NET-DERNIERS-DECES#",
            "table table-hover table-striped table-bordered",
            $content
        );
        $content = preg_replace("|#URL-ANNONCE#|", "http://www.enaos.net", $content);

        return $titre . $content;
    }
}

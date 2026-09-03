<?php


namespace AcMarche\Api\Necrologie;

use DateTime;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use SoapClient;
use Throwable;
use Twig\Environment;

class Necrologie
{
    public function __construct(
        private CacheItemPoolInterface $cache,
        private Environment $environment,
        private LoggerInterface $logger
    ) {

    }

    public function getNecro(bool $fullpage = false): string
    {
        $necrologie = $this->cache->getItem('necrologie_'.$fullpage);

        if (!$necrologie->isHit()) {
            try {
                $enaos = $this->getEnaos();
            } catch (Throwable $throwable) {
                $this->logger->error('Necrologie enaos: '.$throwable->getMessage(), ['exception' => $throwable]);

                //ne pas garder l'erreur en cache jusqu'a demain
                $necrologie->expiresAfter(300);
                $this->cache->save($necrologie->set(''));

                return '';
            }

            if ($fullpage) {
                $html = $this->environment->render(
                    '@AcMarcheApi/marchebe/deces/necrologie.html.twig',
                    [
                        'enaos' => $enaos,
                    ]
                );
            } else {
                $html = $this->environment->render(
                    '@AcMarcheApi/marchebe/deces/_content.html.twig',
                    [
                        'enaos' => $enaos,
                    ]
                );
            }
            $necrologie->expiresAt(new DateTime('tomorrow'));
            $this->cache->save($necrologie->set($html));
        }

        return $necrologie->get();
    }

    private function getEnaos(): string
    {
        /**
         * WSDL embarque: depuis libxml 2.13 le support HTTP (nanohttp) a ete retire,
         * SoapClient ne sait plus telecharger un WSDL distant (SOAP-ERROR: Parsing WSDL).
         * Le endpoint des appels reste celui declare dans le WSDL.
         */
        $url = dirname(__DIR__, 2).'/Resources/wsdl/derniersdeces.wsdl';

        $service = new SoapClient(
            $url, array(
                'soap_version' => SOAP_1_2,
                'trace' => true,
                'connection_timeout' => 4,
                'exceptions' => true,
            )
        );

        $page = 1;
        /**
         *
         * dump($service->__getFunctions());
         * dump($service->__getTypes());
         */

        $params = array(
            'Interlocuteur' => 22,
            'MDP' => '2013-wmb',
            'Pays' => 23,
            'CP' => 6900,
            'Page' => $page,
            'NbreParPage' => 30,
        );

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

        return preg_replace("|#URL-ANNONCE#|", "http://www.enaos.net", $content);
    }
}

<?php

namespace AcMarche\Api\Hades;

Class HadesEvent extends Hades {

    protected $offre = "evenements";

    public function getOffre() {

        return $this->offre;
    }

    public function getItems() {
        $events = $this->getDataFromXml($this->getOffre());

        $date = new \DateTime();
        $start = $date->format("Y-m-d");
        $end = $date->modify('+15 day')->format("Y-m-d");

        $fields = $this->getFields();

        if (isset($events['error'])) {
            return $events;
        }

        $new = array();
        $i = 0;

        foreach ($events as $event) {
            //   var_dump($event);
            $cp = (int) $event->loc_cp;
            if ($cp == 6900) {

                $eveFin = (string) $event->eve_date_fin;
                $eveDebut = (string) $event->eve_date_debut;
                $eve_date_affichage = (string) $event->eve_date_affichage;

                if ($eveDebut >= $start && $eveFin <= $end) {

                    $dateAffichage = explode("/", $eve_date_affichage);

                    if (is_array($dateAffichage)) {
                        array_pop($dateAffichage);

                        $date_affichage = join('/', $dateAffichage);
                        $event->eve_date_affichage = $date_affichage;
                    }

                    foreach ($fields as $mobile => $field) {
                        $new[$i][$mobile] = $event->$field->__toString();
                    }
                    $i++;
                }
            }
        }

        $new = $this->sortEvents($new);

        return $new;
    }

    protected function sortEvents($events) {
        usort($events, function($a, $b) {

            $as = (string) $a['date_debut'];
            $bs = (string) $b['date_debut'];

            if ($as == $bs) {
                return 0;
            }
            return $as > $bs ? 1 : -1;
        });

        return $events;
    }

    /**
     * A gauche les noms requis pour l'appli
     * a droite les noms dans le flux
     * @return array
     */
    public function getFields() {

        $base = array(
            'id' => 'eve_id',
            'titre' => 'eve_titre_fr',
            'description' => 'eve_desc_fr',
                //   'date_maj' => 'lastmod',
                //   'code_CGT' => 'eve_codecgt'
        );

        $complements = array(
            'date_fin' => 'eve_date_fin',
            'date_debut' => 'eve_date_debut',
            'date_affichage' => 'eve_date_affichage',
            'lieu_nom' => 'lieu_nom',
            'lieu_num' => 'lieu_num',
            'adresse' => 'lieu_adr',
            'localite' => 'loc_nom',
            'cp' => 'loc_cp',
            'nom' => 'eve_contact_nom',
            'latitude' => 'lieu_lat',
            'longitude' => 'lieu_long',
            'info' => 'eve_info_fr',
            'telephone' => 'eve_tel',
            'gsm' => '',
            'website' => 'eve_url',
            'email' => 'eve_mail',
            'fax' => 'eve_fax',
            'photo' => 'photo');

        return array_merge($base, $complements);
    }

    public function getImages($post) {

        $photos = get_metadata('hades', $post->ID, $this->getPrefix() . 'photo', true);

        if (!$photos)
            return false;

        if (!is_array($photos))
            $photos = array($photos);

        //   $twig = Hades::twig();
        //   echo $twig->render('images.html.twig', $photos);

        foreach ($photos as $photo) {
            $header = get_headers($photo, 1);

            if ($header['Content-Type'] == 'image/jpeg') {
                ?>
                <a href="<?php echo $photo ?>" rel="groupevent" class="fancybox"><img id="photo_ftlb" src="<?php echo $photo ?>" alt="photo" class="photoHebergement" rel="lightbox-imagesetname"></a>
                <?php
            }
        }
        ?>
        <br clear="all"/>
        <?php
    }

    public static function getPdfAgenda($idEve, $titre) {

        $PathImg = "http://www.ftlb.be/dbimages/docs/";
        $PathFilePdf = $PathImg . "event" . $idEve;

        $url = $PathFilePdf;

        $header = get_headers($url . ".pdf", 1);

        if ($header['Content-Type'] == 'application/pdf') {
            ?>
            <br clear="all"/>Vous pouvez également consulter le pdf suivant :
            <a href="<?php echo $url ?>.pdf"><img id="pdf_ftlb" src="<?php echo plugin_dir_url(__FILE__) ?>/images/pdf.png" alt="pdf"><?php echo $titre ?>.pdf</a>

            <?php
        }
        ?>
        <br clear="all"/>
        <?php
    }

}
?>

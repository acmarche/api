<?php

namespace AcMarche\Api\Hades;

Class HadesCamping extends Hades {

    protected $offre = "campings";
    protected $type = 'Camping';

    public function getOffre() {

        return $this->offre;
    }

    public function getType() {
        return $this->type;
    }

    public function getItems(): array {
        //print_r($events);
        return $this->getDataFromXml($this->getOffre());
    }

    /**
     * A gauche les noms requis pour l'appli
     * a droite les noms dans le flux
     * @return type
     */
    public function getFields(): array {

        $base = array(
            'id' => 'id_cam',
            'titre' => 'camp_titre',
            'description' => 'desc_terrain_fr',
            'date_maj' => 'cam_mod_dat',
            'code_CGT' => 'coord_cgt');

        $complements = array('adresse' => 'camp_adres',
            'localite' => 'loc_nom',
            'cp' => 'loc_cp',
            'nom' => 'contac_nom',
            'codecgt' => 'coord_cgt',
            'latitude' => 'camp_gpsy',
            'longitude' => 'camp_gpsx',
            'telephone' => 'camp_tel1',
            'gsm' => 'camp_gsm',
            'website' => 'camp_url',
            'email' => 'camp_email',
            'fax' => 'camp_fax');

        return array_merge($base, $complements);
    }

}

<?php

namespace AcMarche\Api\Hades;

Class HadesHotel extends Hades {

    protected $offre = "hotels";
    protected $type = 'Hôtel';

    public function getOffre() {

        return $this->offre;
    }

    public function getType() {
        return $this->type;
    }

    public function getItems() {
        $events = $this->getDataFromXml($this->getOffre());
        return $events;
    }

    public function filtre($param) {

        if (isset($log->$cp) && $log->$cp == '6900') {

        }
    }

    /**
     * A gauche les noms requis pour l'appli
     * a droite les noms dans le flux
     * @return type
     */
    public function getFields() {

        $base = array(
            'id' => 'hot_id',
            'titre' => 'hot_titre',
            'description' => 'hot_desc_fr',
            'date_maj' => 'hot_mod_dat',
            'code_CGT' => 'hot_codecgt');

        $complements = array('adresse' => 'hot_adresse',
            'localite' => 'loc_nom',
            'cp' => 'loc_cp',
            'nom' => 'hot_contact_nom',
            'codecgt' => 'hot_codecgt',
            'latitude' => 'hot_gpsy',
            'longitude' => 'hot_gpsx',
            'ouverture_indiv' => 'hot_ferm_fr',
            'tarifs' => 'hot_remarque_fr',
            'telephone' => 'hot_telephone',
            'gsm' => '',
            'website' => 'hot_url',
            'email' => 'hot_email',
            'fax' => 'hot_fax');

        return array_merge($base, $complements);
    }

}

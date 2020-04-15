<?php

namespace AcMarche\Api\Hades;

Class HadesGite extends Hades {

    protected $offre = "gites";
    protected $type = 'Gîte';

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

    /**
     * A gauche les noms requis pour l'appli
     * a droite les noms dans le flux
     * @return type
     */
    public function getFields() {

        $base = array(
            'id' => 'git_id',
            'titre' => 'git_titre',
            'description' => 'git_desc_com_fr',
            'date_maj' => 'lastmod',
            'code_CGT' => 'git_codecgt');

        $complements = array('adresse' => 'git_adresse',
            'localite' => 'loc_nom',
            'cp' => 'loc_cp',
            'nom' => 'git_contact_nom',
            'codecgt' => 'git_codecgt',
            'latitude' => 'git_gpsy',
            'longitude' => 'git_gpsx',
            'telephone' => 'git_contact_tel',
            'gsm' => 'git_contact_tel2',
            'website' => 'git_url',
            'email' => 'git_email',
            'fax' => 'git_contact_fax');

        return array_merge($base, $complements);
    }

}

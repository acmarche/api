<?php

namespace AcMarche\Api\Hades;

Class HadesChambre extends Hades {

    protected $offre = "chambres";
    protected $type = 'Chambre d\'hôte';

    public function getOffre() {

        return $this->offre;
    }

    public function getType() {
        return $this->type;
    }

    public function getItems(): array {
        return $this->getDataFromXml($this->getOffre());
    }

    /**
     * A gauche les noms requis pour l'appli
     * a droite les noms dans le flux
     * @return type
     */
    public function getFields(): array {


        $base = array(
            'id' => 'chb_id',
            'titre' => 'chb_titre',
            'description' => 'chb_desc_com_fr',
            'date_maj' => 'lastmod',
            'code_CGT' => 'chb_codecgt');

        $complements = array('adresse' => 'chb_pro_adresse',
            'localite' => 'loc_nom',
            'cp' => 'loc_cp',
            'nom' => 'chb_pro_nom',
            'codecgt' => 'chb_codecgt',
            'latitude' => 'chb_gpsy',
            'longitude' => 'chb_gpsx',
            'tarifs' => 'chb_rem_prix_fr',
            'telephone' => 'chb_pro_tel',
            'gsm' => 'chb_pro_tel2',
            'website' => 'chb_url',
            'email' => 'chb_email',
            'fax' => 'chb_pro_fax');

        return array_merge($base, $complements);
    }

}

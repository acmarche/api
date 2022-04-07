<?php

namespace AcMarche\Api\Hades;

abstract class Hades {

    protected $url = "http://www.ftlb.be/rss/xmlinterreg.php";
    protected $params = "?pays=9&quoi=tout&offre=";

    public function getUrl() {

        return $this->url . $this->getParams();
    }

    public function getParams() {
        return $this->params;
    }

    /**
     * Charge le flux rss
     * @param type $offre evenements, hotels, gites, chambres, campings
     * @return array
     */
    public function getDataFromXml($offre) {

        $url = $this->getUrl() . $offre;
        libxml_use_internal_errors(true);

        $flux = @simplexml_load_file($url);

        if ($flux) {
            return $flux->children();
        }

        $error = libxml_get_errors();
        $message = $error[0]->message;

        return array('error' => $message);
    }

    /**
     * Renomme les noms donne par la ftlb pour standardiser
     * @param type $object
     * @return type
     */
    public function getItemsAndRename($object) {

        $items = $object->getItems();
        $fields = $object->getFields();
        $type = $object->getType();

        if (isset($items['error'])) {
            return $items;
        }

        $new = array();
        $i = 0;

        foreach ($items as $item) {

            if (property_exists($item, 'loc_cp') && $item->loc_cp !== null && $item->loc_cp == '6900') {

                foreach ($fields as $mobile => $field) {
                    $new[$i][$mobile] = (string) $item->$field;
                }

                $new[$i]['logement_type'] = $type;
                $i++;
            }
        }

        return $new;
    }

}

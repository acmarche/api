<?php

namespace AcMarche\Api\Hades;

Class HadesLogement extends Hades {

    public function getAll(): array {

        $hadesHotel = new HadesHotel();
        $hotels = $hadesHotel->getItemsAndRename($hadesHotel);

        $hadesCamping = new HadesCamping();
        $campings = $hadesCamping->getItemsAndRename($hadesCamping);

        $hadesChambre = new HadesChambre();
        $chambres = $hadesChambre->getItemsAndRename($hadesChambre);

        $hadesGite = new HadesGite();
        $gites = $hadesGite->getItemsAndRename($hadesGite);

        return array_merge($hotels, $campings, $chambres, $gites);
    }

    public function getImages($post) {

        $photos = [];
        $key = 'hades';
        $photos['photos'] = get_metadata($key, $post->ID, $this->getPrefix() . 'photo', true);

        if ($photos === [])
            return false;

        $twig = Hades::twig();

        echo $twig->render('images.html.twig', $photos);
    }

    public function getRubriques(): array {
        return array(
            'hotels' => 'Hôtels',
            'gites' => 'Gîtes',
            'chambres' => 'Chambre d\'hôtes',
            'campings' => 'Camping');
    }

}

?>

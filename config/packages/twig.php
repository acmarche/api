<?php

use Symfony\Config\TwigConfig;

return static function (TwigConfig $twig) {
    $twig
        ->formThemes(['bootstrap_4_layout.html.twig'])
        ->path('%kernel.project_dir%/src/AcMarche/Bottin/templates', 'AcMarcheApi')
        ->global('bootcdn')->value('https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css');
};

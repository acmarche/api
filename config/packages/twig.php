<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('twig', [
        'form_themes' => ['bootstrap_4_layout.html.twig'],
        'paths' => [
            '%kernel.project_dir%/src/AcMarche/Api/templates' => 'AcMarcheApi',
        ],
        'globals' => [
            'bootcdn' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css',
        ],
    ]);
};

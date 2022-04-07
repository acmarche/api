<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'doctrine',
        [
            'orm' => [
                'mappings' => [
                    'AcMarche\Api' => [
                        'is_bundle' => false,
                        'type' => 'attribute',
                        'dir' => '%kernel.project_dir%/src/AcMarche/Api/src/Entity',
                        'prefix' => 'AcMarche\Api',
                        'alias' => 'AcMarche\Api',
                    ],
                ],
            ],
        ]
    );
};

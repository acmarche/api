<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('doctrine', [
        'dbal' => [
            'connections' => [
                'default' => [
                    'url' => '%env(resolve:DATABASE_URL)%',
                    'charset' => 'utf8mb4',
                ],
            ],
        ],
        'orm' => [
            'entity_managers' => [
                'default' => [
                    'connection' => 'default',
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
            ],
        ],
    ]);
};

<?php

use Symfony\Config\DoctrineConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\Env;

return static function (DoctrineConfig $doctrine) {

    $doctrine
        ->dbal()
        ->connection('default')
        ->url(env('DATABASE_URL')->resolve())
        ->charset('utf8mb4');

    $emApi = $doctrine->orm()->entityManager('default');
    $emApi->connection('default');
    $emApi->mapping('AcMarche\Api')
        ->isBundle(false)
        ->type('attribute')
        ->dir('%kernel.project_dir%/src/AcMarche/Api/src/Entity')
        ->prefix('AcMarche\Api')
        ->alias('AcMarche\Api');

};

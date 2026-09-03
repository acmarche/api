<?php

use AcMarche\Api\Entity\User;
use AcMarche\Api\Security\AccessTokenHandler;
use AcMarche\Api\Security\ApiAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('security', [
        'providers' => [
            'api_user_provider' => [
                'entity' => [
                    'class' => User::class,
                    'manager_name' => 'default',
                    'property' => 'username',
                ],
            ],
        ],
        'firewalls' => [
            'dev' => [
                'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
                'security' => false,
            ],
            'main' => [
                'lazy' => true,
                'provider' => 'api_user_provider',
                'logout' => [
                    'path' => 'app_logout',
                ],
                'form_login' => [
                    'login_path' => 'app_login',
                    'check_path' => 'app_login',
                    'default_target_path' => 'api_home',
                    'remember_me' => true,
                    'enable_csrf' => true,
                ],
                'custom_authenticators' => [ApiAuthenticator::class],
                'entry_point' => ApiAuthenticator::class,
                'login_throttling' => [
                    'max_attempts' => 6,
                    'interval' => '15 minutes',
                ],
                'remember_me' => [
                    'secret' => '%kernel.secret%',
                    'lifetime' => 604800,
                    'path' => '/',
                    'always_remember_me' => true,
                ],
                'access_token' => [
                    'token_handler' => AccessTokenHandler::class,
                ],
            ],
        ],
    ]);
};

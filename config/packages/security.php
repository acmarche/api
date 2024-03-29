<?php


use AcMarche\Api\Entity\User;
use AcMarche\Api\Security\ApiAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'password_hashers' => [
            User::class => ['algorithm' => 'auto'],
        ],
    ]);

    $containerConfigurator->extension(
        'security',
        [
            'providers' => [
                'api_user_provider' => [
                    'entity' => [
                        'class' => User::class,
                        'property' => 'username',
                    ],
                ],
            ],
        ]
    );

    $authenticators = [ApiAuthenticator::class];

    $main = [
        'lazy' => true,
        'provider' => 'api_user_provider',
        'logout' => ['path' => 'app_logout'],
        'form_login' => [],
        'entry_point' => ApiAuthenticator::class,
    ];

    $main['custom_authenticator'] = $authenticators;

    $api = [
        'pattern' =>
            '^/bottin',
        'http_basic' => [
            'realm' => 'Secured Area',
            'provider' => 'api_user_provider',
        ],
    ];

    $containerConfigurator->extension(
        'security',
        [
            'firewalls' => [
                //'api_protect' => $api,
                'main' => $main,
            ],
        ]
    );
};

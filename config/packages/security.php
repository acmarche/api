<?php

use AcMarche\Api\Entity\User;
use AcMarche\Api\Security\AccessTokenHandler;
use AcMarche\Api\Security\ApiAuthenticator;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    $security
        ->provider('api_user_provider')
        ->entity()
        ->class(User::class)
        ->managerName('default')
        ->property('username');

    $security
        ->firewall('dev')
        ->pattern('^/(_(profiler|wdt)|css|images|js)/')
        ->security(false);

    $mainFirewall = $security
        ->firewall('main')
        ->lazy(true);

    $mainFirewall
        ->logout()
        ->path('app_logout');

    $mainFirewall
        ->formLogin()
        ->loginPath('app_login')
        ->checkPath('app_login')
        ->defaultTargetPath('api_home')
        ->rememberMe(true)
        ->enableCsrf(true);

    $authenticators = [ApiAuthenticator::class];

    $mainFirewall
        ->customAuthenticators($authenticators)
        ->provider('api_user_provider')
        ->entryPoint(ApiAuthenticator::class)
        ->loginThrottling()
        ->maxAttempts(6)
        ->interval('15 minutes');

    $mainFirewall
        ->rememberMe([
            'secret' => '%kernel.secret%',
            'lifetime' => 604800,
            'path' => '/',
            'always_remember_me' => true,
        ]);

    $mainFirewall
        ->accessToken()
        ->tokenHandler(AccessTokenHandler::class);
};
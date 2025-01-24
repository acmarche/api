<?php

namespace AcMarche\Api\Security;

use AcMarche\Api\Entity\User;
use Firebase\JWT\JWT;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class TokenGenerator
{
    public function __construct(#[Autowire(env: 'APP_SECRET')] #[\SensitiveParameter] private readonly string $secretKey,
    ) {}

    public function generateToken(User $user): string
    {
        $payload = [
            'iss' => 'api-parking', // Issuer
            'aud' => 'marche.be', // Audience
            'iat' => time(), // Issued at
            'nbf' => time(), // Not before
            'exp' => time() + 3600, // Expiration time (1 hour from now)
            'user_id' => $user->getId(),
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }
}
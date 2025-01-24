<?php

namespace AcMarche\Api\Security;

use AcMarche\Api\Repository\AccessTokenRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private AccessTokenRepository $accessTokenRepository,
        private LoggerInterface $logger,
    ) {}

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        $token = $this->accessTokenRepository->findOneByToken($accessToken);

        if (null === $token || !$token->isValid()) {
            throw new BadCredentialsException('Invalid token.');
        }
        $this->logger->debug("zeze ok username ".$token->user->getUserIdentifier());
        // and return a UserBadge object containing the user identifier from the found token
        // (this is the same identifier used in Security configuration; it can be an email,
        // a UUID, a username, a database ID, etc.)
        return new UserBadge($token->user->getUserIdentifier());
    }
}
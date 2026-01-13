<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class StaticTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        #[Autowire(env: 'API_SECRET_KEY')]
        private string $apiSecret
    ) {
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        if ($accessToken !== $this->apiSecret) {
            throw new BadCredentialsException('Invalid API Key');
        }

        // Retourne un UserBadge pour un utilisateur identifié par "api_client"
        return new UserBadge('api_client');
    }
}

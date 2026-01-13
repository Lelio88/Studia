<?php

namespace App\Security;

use Symfony\Component\Security\Http\AccessToken\AccessTokenExtractorInterface;
use Symfony\Component\HttpFoundation\Request;

class ApiKeyExtractor implements AccessTokenExtractorInterface
{
    public function extractAccessToken(Request $request): ?string
    {
        return $request->headers->get('X-API-KEY');
    }
}

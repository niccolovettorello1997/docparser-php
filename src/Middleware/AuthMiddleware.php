<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Middleware;

use Niccolo\DocparserPhp\Controller\Responses\ErrorResponse;

class AuthMiddleware
{
    /**
     * Handle authentication.
     *
     * @return Response|null
     */
    public function handle(): ?Response
    {
        if ($_ENV['AUTH_REQUIRED'] !== '1') {
            return null;
        }

        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!preg_match('/Bearer\s+(.+)/', $header, $m)) {
            return new ErrorResponse(
                statusCode: 401,
                content: 'Missing Authorization header',
                code: 'ERR_NO_AUTH_HEADER'
            );
        }

        $token = $m[1];

        if ($token !== getenv('API_TOKEN')) {
            return new ErrorResponse(
                statusCode: 401,
                content: 'Invalid token',
                code: 'ERR_INVALID_TOKEN'
            );
        }

        return null;
    }    
}

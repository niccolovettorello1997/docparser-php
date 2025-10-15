<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Middleware;

use Niccolo\DocparserPhp\Controller\Responses\Response;

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
            return new Response(
                statusCode: 401,
                content: json_encode(['error' => 'Missing Authorization header'])
            );
        }

        $token = $m[1];

        if ($token !== getenv('API_TOKEN')) {
            return new Response(
                statusCode: 401,
                content: json_encode(['error' => 'Invalid token'])
            );
        }

        return null;
    }    
}

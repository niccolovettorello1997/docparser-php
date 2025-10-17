<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Middleware;

use Niccolo\DocparserPhp\Model\Utils\Error\Enum\ErrorCode;
use Niccolo\DocparserPhp\Controller\Responses\ErrorResponse;
use Niccolo\DocparserPhp\Controller\Responses\BaseResponse;

class AuthMiddleware
{
    /**
     * Handle authentication.
     *
     * @return BaseResponse|null
     */
    public function handle(): ?BaseResponse
    {
        if ($_ENV['AUTH_REQUIRED'] !== '1') {
            return null;
        }

        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!preg_match('/Bearer\s+(.+)/', $header, $m)) {
            return new ErrorResponse(
                statusCode: 401,
                content: 'Missing Authorization header',
                code: ErrorCode::NO_AUTH_HEADER->value
            );
        }

        $token = $m[1];

        if ($token !== getenv('API_TOKEN')) {
            return new ErrorResponse(
                statusCode: 401,
                content: 'Invalid token',
                code: ErrorCode::INVALID_TOKEN->value
            );
        }

        return null;
    }    
}

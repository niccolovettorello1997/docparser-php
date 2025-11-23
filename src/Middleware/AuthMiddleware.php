<?php

declare(strict_types=1);

namespace DocparserPhp\Middleware;

use DocparserPhp\Config\Config;
use DocparserPhp\Controller\Responses\BaseResponse;
use DocparserPhp\Controller\Responses\ErrorResponse;
use DocparserPhp\Model\Utils\Error\Enum\ErrorCode;

class AuthMiddleware
{
    public function __construct(
        private readonly Config $config,
    ) {
    }

    /**
     * Handle authentication.
     *
     * @return BaseResponse|null
     */
    public function handle(): ?BaseResponse
    {
        if ($this->config->get('AUTH_REQUIRED') !== 1) {
            return null;
        }

        /** @var string $header */
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

<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Controller\Responses;

class BaseResponse
{
    public function __construct(
        private readonly int $statusCode = 200,
        private readonly string $content = 'ok',
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}

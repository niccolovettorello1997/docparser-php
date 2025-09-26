<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Controller\Utils;

class Response
{
    public function __construct(
        private readonly int $statusCode,
        private readonly string $content,
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

<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Controller\Responses;

class Response
{
    public function __construct(
        private readonly int $statusCode,
        private readonly array $content,
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getContent(): string
    {
        return json_encode($this->content);
    }
}

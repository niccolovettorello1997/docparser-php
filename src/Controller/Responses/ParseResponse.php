<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Controller\Responses;

class ParseResponse extends BaseResponse
{
    private string $requestId;
    private int $durationMs;
    private int $sizeBytes;
    private string $version;

    public function __construct(
        int $statusCode,
        string $content,
        string $requestId,
        int $durationMs,
        int $sizeBytes,
        string $version
    ) {
        parent::__construct(
            statusCode: $statusCode,
            content: $content
        );

        $this->requestId = $requestId;
        $this->durationMs = $durationMs;
        $this->sizeBytes = $sizeBytes;
        $this->version = $version;
    }

    public function getRequestId(): string
    {
        return $this->requestId;

    }

    public function getDurationMs(): int
    {
        return $this->durationMs;
    }

    public function getSizeBytes(): int
    {
        return $this->sizeBytes;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getContent(): string
    {
        return json_encode(
            [
                'status' => 'ok',
                'requestId' => $this->getRequestId(),
                'parsed' => parent::getContent(),
                'meta' => [
                    'durationMs' => $this->getDurationMs(),
                    'sizeBytes' => $this->getSizeBytes(),
                    'version' => $this->getVersion(),
                ]
            ]
        );
    }
}


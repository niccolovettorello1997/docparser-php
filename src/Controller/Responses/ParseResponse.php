<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Controller\Responses;

class ParseResponse extends BaseResponse
{
    private string $requestId;
    private array $validation;
    private array $parsed;
    private int $durationMs;
    private int $sizeBytes;
    private string $version;

    public function __construct(
        int $statusCode,
        string $requestId,
        array $validation,
        array $parsed,
        int $durationMs,
        int $sizeBytes,
        string $version
    ) {
        parent::__construct(
            statusCode: $statusCode,
            content: ''
        );

        $this->requestId = $requestId;
        $this->validation = $validation;
        $this->parsed = $parsed;
        $this->durationMs = $durationMs;
        $this->sizeBytes = $sizeBytes;
        $this->version = $version;
    }

    public function getRequestId(): string
    {
        return $this->requestId;
    }

    public function getValidation(): array
    {
        return $this->validation;
    }

    public function getParsed(): array
    {
        return $this->parsed;
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
                'validation' => $this->getValidation(),
                'parsed' => $this->getParsed(),
                'meta' => [
                    'durationMs' => $this->getDurationMs(),
                    'sizeBytes' => $this->getSizeBytes(),
                    'version' => $this->getVersion(),
                ]
            ]
        );
    }
}


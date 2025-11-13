<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Controller\Responses;

class ParseResponse extends BaseResponse
{
    private string $requestId;
    /** @var array{Valid: 'yes'|'no', Errors: array<array<string, string>>, Warnings: array<array<string, string>>} $validation */
    private array $validation;
    /** @var array<string, mixed> $parsed */
    private array $parsed;
    private int $durationMs;
    private int $sizeBytes;
    private ?string $version;

    /**
     * @param int                                                                                                    $statusCode
     * @param string                                                                                                 $requestId
     * @param array{Valid: 'yes'|'no', Errors: array<array<string, string>>, Warnings: array<array<string, string>>} $validation
     * @param array<string, mixed>                                                                                   $parsed
     * @param int                                                                                                    $durationMs
     * @param int                                                                                                    $sizeBytes
     * @param ?string                                                                                                $version
     */
    public function __construct(
        int $statusCode,
        string $requestId,
        array $validation,
        array $parsed,
        int $durationMs,
        int $sizeBytes,
        ?string $version = null
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

    /**
     * @return array{Valid: 'yes'|'no', Errors: array<array<string, string>>, Warnings: array<array<string, string>>}
     */
    public function getValidation(): array
    {
        return $this->validation;
    }

    /**
     * @return array<string, mixed>
     */
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

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function getContent(): string
    {
        $encodedJson = json_encode(
            [
                'status' => 'ok',
                'requestId' => $this->getRequestId(),
                'validation' => $this->getValidation(),
                'parsed' => $this->getParsed(),
                'meta' => [
                    'durationMs' => $this->getDurationMs(),
                    'sizeBytes' => $this->getSizeBytes(),
                    'version' => $this->getVersion() ?? 'dev',
                ]
            ]
        );

        return (false !== $encodedJson) ? $encodedJson : 'Error while returning parse response content';
    }
}

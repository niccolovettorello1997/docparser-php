<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Controller\Responses;

class ErrorResponse extends BaseResponse
{
    private string $code;
    private ?string $details = null;

    public function __construct(
        int $statusCode,
        string $content,
        string $code,
        ?string $details = null
    ) {
        parent::__construct(
            statusCode: $statusCode,
            content: $content
        );

        $this->code = $code;
        $this->details = $details;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function getContent(): string
    {
        $encodedContent = json_encode(
            [
                'status' => 'error',
                'code' => $this->getCode(),
                'content' => parent::getContent(),
                'details' => $this->getDetails() ?? '',
            ]
        );

        return (false !== $encodedContent) ? $encodedContent : 'Error while returning error response content';
    }
}

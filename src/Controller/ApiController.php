<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Controller;

use Niccolo\DocparserPhp\Service\ParserService;

class ApiController
{
    public function __construct(
        private readonly ParserService $parserService
    ) {
    }

    public function parseFile(): void
    {
        // No file was uploaded
        if (!isset($_FILES['file'])) {
            http_response_code(response_code: 400);
            echo json_encode(value: ['error' => 'No file uploaded']);
            return;
        }

        $result = $this->parserService->parseUploadedFile(file: $_FILES['file']);

        echo json_encode(value: ['parsed' => $result]);
    }

    public function parseText(): void
    {
        // Read raw body
        $raw = file_get_contents(filename: 'php://input');

        // Raw body empty
        if (empty($raw)) {
            http_response_code(response_code: 400);
            echo json_encode(value: ['error' => 'Empty input']);
            return;
        }

        $result = $this->parserService->parseText(text: $raw);

        echo json_encode(value: ['parsed' => $result]);
    }

    public function parseJson(): void
    {
        $data = json_decode(json: file_get_contents(filename: 'php://input'), associative: true);

        // Wrong request format
        if (!is_array(value: $data) || !isset($data['data'])) {
            http_response_code(response_code: 400);
            echo json_encode(value: ['error' => 'Invalid JSON']);
            return;
        }

        $result = $this->parserService->parseJson(data: $data);

        echo json_encode(value: ['parsed' => $result]);
    }
}
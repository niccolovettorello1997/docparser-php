<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Controller;

use Niccolo\DocparserPhp\Controller\Responses\Response;
use Niccolo\DocparserPhp\Service\ParserService;
use Niccolo\DocparserPhp\Service\ValidatorService;
use Niccolo\DocparserPhp\Service\Utils\Query;
use Niccolo\DocparserPhp\View\Parser\JsonParserView;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\RenderingType;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\InputType;

class ApiController
{
    public function __construct(
        private readonly ValidatorService $validatorService,
        private readonly ParserService $parserService
    ) {
    }

    /**
     * Parse the content of an uploaded file.
     * 
     * @return Response
     */
    public function parseFile(): Response
    {
        // Handle type
        $type = $_POST['type'];

        // Missing input type
        if (!isset($type)) {
            return new Response(
                statusCode: 400,
                content: ['error' => "Missing 'type' field"],
            );
        }

        $inputType = InputType::tryFrom($type);

        // Input type not supported
        if (null === $inputType) {
            return new Response(
                statusCode: 400,
                content: ['error' => 'Input type not supported'],
            );
        }

        // Handle file
        // No file uploaded
        if (!isset($_FILES['document'])) {
            return new Response(
                statusCode: 400,
                content: ['error' => 'No file uploaded'],
            );
        }

        $file = $_FILES['document'];

        // Upload error
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return new Response(
                statusCode: 400,
                content: ['error' => \sprintf("Upload error: %s", $file['error'])],
            );
        }

        // Create Query
        $query = new Query(
            context: file_get_contents($file['tmp_name']),
            inputType: $inputType,
        );

        // Validate
        $validationResult = $this->validatorService->runValidation(query: $query);

        // Parse if validation completed without errors
        $parseResult = ($validationResult->isValid()) ? $this->parserService->parse(query: $query) : null;

        return new Response(
            statusCode: 200,
            content: [
                'validation' => $validationResult->toArray(),
                'parsing' => $parseResult?->toArray() ?? [],
            ]
        );
    }
}

<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Controller;

use Niccolo\DocparserPhp\Controller\Responses\ParseResponse;
use Niccolo\DocparserPhp\Controller\Responses\ErrorResponse;
use Niccolo\DocparserPhp\Controller\Responses\BaseResponse;
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
     * Run validation and parsing and return a response.
     *
     * @param  Query $query
     * @return ParseResponse
     */
    private function validateAndParse(Query $query): ParseResponse
    {
        // Identify request
        $requestId = 'req-' . bin2hex(random_bytes(8));

        // Start stopwatch
        $start = microtime(true);

        // Validate
        $validationResult = $this->validatorService->runValidation(query: $query);

        // Parse if validation completed without errors
        $parseResult = ((null !== $validationResult) && ($validationResult->isValid())) ? $this->parserService->parse(query: $query) : null;

        $durationMs = (int) ((microtime(true) - $start) * 1000);
        
        $jsonParserView = new JsonParserView(
            elementValidationResult: $validationResult,
            tree: $parseResult
        );

        return new Response(
            statusCode: 200,
            content: $jsonParserView->render(),
            requestId: $requestId,
            durationMs: $durationMs,
            sizeBytes: strlen($query->getContext()),
            version: $this->parserService->getVersion()
        );
    }

    /**
     * Parse the content of an uploaded file.
     * 
     * @return BaseResponse
     */
    public function parseFile(): BaseResponse
    {
        // Handle type
        $type = $_POST['type'];

        // Missing input type
        if (!isset($type)) {
            return new ErrorResponse(
                statusCode: 400,
                content: "Missing 'type' field",
                code: 'ERR_MISSING_REQUIRED_FIELD'
            );
        }

        $inputType = InputType::tryFrom($type);

        // Input type not supported
        if (null === $inputType) {
            return new ErrorResponse(
                statusCode: 400,
                content: 'Input type not supported',
                code: 'ERR_SUPPORTED_TYPE'
            );
        }

        // Handle file
        // No file uploaded
        if (!isset($_FILES['document'])) {
            return new ErrorResponse(
                statusCode: 400,
                content: 'No file uploaded',
                code: 'ERR_NO_FILE_UPLOADED'
            );
        }

        $file = $_FILES['document'];

        // Upload error
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return new ErrorResponse(
                statusCode: 400,
                content: 'Upload error',
                code: 'ERR_FILE_UPLOAD',
                details: $file['error']
            );
        }

        // Create Query
        $query = new Query(
            context: file_get_contents($file['tmp_name']),
            inputType: $inputType,
        );
        
        return $this->validateAndParse(query: $query);
    }

    /**
     * Parse the content of the JSON request.
     *
     * @return BaseResponse
     */
    public function parseJson(): BaseResponse
    {
        // Handle request content
        $rawInput = file_get_contents('php://input', true);

        if (false === $rawInput) {
            return new ErrorResponse(
                statusCode: 400,
                content: 'Could not read request content'])
            );
        }

        $request = json_decode($rawInput, true);

        if (null === $request) {
            return new Response(
                statusCode: 400,
                content: json_encode(['error' => 'Could not read request content'])
            );
        }

        if (!isset($request['content']) || empty($request['content'])) {
            return new Response(
                statusCode: 400,
                content: json_encode(['error' => "Required field 'content' is missing or empty"])
            );
        }

        // Handle request type
        if (!isset($request['type'])) {
            return new Response(
                statusCode: 400,
                content: json_encode(['error' => "Missing required 'type' field"])
            );
        }

        $inputType = InputType::tryFrom($request['type']);

        if (null === $inputType) {
            return new Response(
                statusCode: 400,
                content: json_encode(['error' => 'Type not supported'])
            );
        }

        // Create Query
        $query = new Query(
            context: urldecode($request['content']),
            inputType: $inputType,
        );
        
        return $this->validateAndParse(query: $query);
    }
}

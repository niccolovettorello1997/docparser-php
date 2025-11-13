<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Controller;

use Niccolo\DocparserPhp\Controller\Responses\BaseResponse;
use Niccolo\DocparserPhp\Controller\Responses\ErrorResponse;
use Niccolo\DocparserPhp\Controller\Responses\ParseResponse;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;
use Niccolo\DocparserPhp\Model\Utils\Error\Enum\ErrorCode;
use Niccolo\DocparserPhp\Model\Utils\Error\InternalError;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\InputType;
use Niccolo\DocparserPhp\Service\ParserService;
use Niccolo\DocparserPhp\Service\Utils\Query;
use Niccolo\DocparserPhp\Service\ValidatorService;

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
     * @param Query $query
     *
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

        if (null === $validationResult) {
            $validationResult = new ElementValidationResult();

            $validationResult->addError(
                error: new InternalError(
                    message: 'An internal error occurred while running validation'
                )
            );
        }

        // Parse if validation completed without errors
        $parseResult = ($validationResult->isValid()) ? $this->parserService->parse(query: $query) : null;

        $durationMs = (int) ((microtime(true) - $start) * 1000);

        return new ParseResponse(
            statusCode: 200,
            requestId: $requestId,
            validation: $validationResult->toArray(),
            parsed: $parseResult?->toArray() ?? [],
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
        // Missing input type
        if (!array_key_exists('type', $_POST)) {
            return new ErrorResponse(
                statusCode: 400,
                content: "Missing required 'type' field",
                code: ErrorCode::MISSING_REQUIRED_FIELD->value
            );
        }

        /** @var string $type */
        $type = $_POST['type'];

        $inputType = InputType::tryFrom($type);

        // Input type not supported
        if (null === $inputType) {
            return new ErrorResponse(
                statusCode: 400,
                content: 'Input type not supported',
                code: ErrorCode::UNSUPPORTED_TYPE->value
            );
        }

        // Handle file
        // No file uploaded
        if (!isset($_FILES['document'])) {
            return new ErrorResponse(
                statusCode: 400,
                content: 'No file uploaded',
                code: ErrorCode::NO_FILE_UPLOADED->value
            );
        }

        /** @var array{name:string,type:string,tmp_name:string,error:int,size:int} $file */
        $file = $_FILES['document'];

        // Upload error
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return new ErrorResponse(
                statusCode: 409,
                content: 'Upload error',
                code: ErrorCode::UPLOAD_ERROR->value,
            );
        }

        $fileContent = @file_get_contents($file['tmp_name']);

        if (false === $fileContent) {
            return new ErrorResponse(
                statusCode: 500,
                content: 'Could not read request content',
                code: ErrorCode::INTERNAL_SERVER_ERROR->value
            );
        }

        // Create Query
        $query = new Query(
            context: $fileContent,
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

        /** @var null|array<string, string> $request */
        $request = (false !== $rawInput) ? json_decode($rawInput, true) : null;

        if (null === $request) {
            return new ErrorResponse(
                statusCode: 500,
                content: 'Could not read request content',
                code: ErrorCode::INTERNAL_SERVER_ERROR->value
            );
        }

        if (!isset($request['content']) || empty($request['content'])) {
            return new ErrorResponse(
                statusCode: 400,
                content: "Missing required 'content' field",
                code: ErrorCode::MISSING_REQUIRED_FIELD->value
            );
        }

        // Handle request type
        if (!isset($request['type'])) {
            return new ErrorResponse(
                statusCode: 400,
                content: "Missing required 'type' field",
                code: ErrorCode::MISSING_REQUIRED_FIELD->value
            );
        }

        /** @var string $requestType */
        $requestType = $request['type'];

        $inputType = InputType::tryFrom($requestType);

        if (null === $inputType) {
            return new ErrorResponse(
                statusCode: 400,
                content: 'Input type not supported',
                code: ErrorCode::UNSUPPORTED_TYPE->value
            );
        }

        /** @var string $content */
        $content = $request['content'];

        // Create Query
        $query = new Query(
            context: urldecode($content),
            inputType: $inputType,
        );

        return $this->validateAndParse(query: $query);
    }
}

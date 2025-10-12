<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Controller;

use Niccolo\DocparserPhp\Controller\Responses\Response;
use Niccolo\DocparserPhp\Service\ParserService;
use Niccolo\DocparserPhp\Controller\Utils\Query;
use Niccolo\DocparserPhp\View\Parser\JsonParserView;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\RenderingType;

class ApiController
{
    public function __construct(
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
        try {
            $query = Query::getQuery(
                data: [
                    'type' => $_POST['type'],
                    'renderingType' => RenderingType::JSON->value,
                ],
                files: $_FILES,
            );
        } catch (\InvalidArgumentException $e) {
            return new Response(
                statusCode: 400,
                content: $e->getMessage()
            );
        }

        // Run validation
        $validationResult = $this->parserService->runValidation(query: $query);
        $parseResult = $this->parserService->parseUploadedFile(query: $query);

        $render = (new JsonParserView(tree: $resultTree))->render();

        return new Response(
            statusCode: 200,
            content: $render
        );
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
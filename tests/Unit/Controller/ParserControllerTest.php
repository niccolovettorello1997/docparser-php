<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Controller;

use Niccolo\DocparserPhp\Controller\ParserController;
use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\Service\ParserService;
use Niccolo\DocparserPhp\Service\ValidatorService;
use Niccolo\DocparserPhp\View\HtmlParserView;
use Niccolo\DocparserPhp\View\JsonParserView;
use PHPUnit\Framework\TestCase;

class ParserControllerTest extends TestCase
{
    public function test_handle_request_with_unsupported_input_type(): void
    {
        $validatorService = new ValidatorService();
        $parserService = new ParserService();

        $controller = new ParserController(
            validatorService: $validatorService,
            parserService: $parserService
        );

        // Unsupported type
        $result = $controller->handleRequest(
            data: [
                'context' => '<html></html>',
                'type' => 'xml',
                'renderingType' => 'html',
            ]
        );

        $rendered = $result->render();

        $this->assertInstanceOf(
            HtmlParserView::class,
            $result
        );
        $this->assertStringContainsString(
            'Unsupported input type: xml',
            $rendered
        );
    }

    public function test_handle_request_unsupported_rendering_type_defaults_to_json(): void
    {
        $validatorService = new ValidatorService();
        $parserService = new ParserService();

        $controller = new ParserController(
            validatorService: $validatorService,
            parserService: $parserService
        );

        // Unsupported type
        $result = $controller->handleRequest(
            data: [
                'context' => '<html></html>',
                'type' => 'html',
                'renderingType' => 'xml',
            ]
        );

        $rendered = $result->render();

        $this->assertInstanceOf(
            JsonParserView::class,
            $result
        );
    }

    public function test_handle_request_html_with_empty_context(): void
    {
        $validatorService = new ValidatorService();
        $parserService = new ParserService();

        $controller = new ParserController(
            validatorService: $validatorService,
            parserService: $parserService
        );

        // Empty context
        $result = $controller->handleRequest(
            data: [
                'context' => '',
                'type' => 'html',
                'renderingType' => 'html',
            ]
        );

        $rendered = $result->render();

        $this->assertInstanceOf(
            HtmlParserView::class,
            $result
        );
        $this->assertStringContainsString(
            'No context provided',
            $rendered
        );
    }

    public function test_handle_request_markdown_with_empty_context(): void
    {
        $validatorService = new ValidatorService();
        $parserService = new ParserService();

        $controller = new ParserController(
            validatorService: $validatorService,
            parserService: $parserService
        );

        // Empty context
        $result = $controller->handleRequest(
            data: [
                'context' => '',
                'type' => 'markdown',
                'renderingType' => 'html',
            ]
        );

        $rendered = $result->render();

        $this->assertInstanceOf(
            HtmlParserView::class,
            $result
        );
        $this->assertStringContainsString(
            'No context provided',
            $rendered
        );
    }

    public function test_handle_request_with_invalid_html(): void
    {
        $validatorService = new ValidatorService();
        $parserService = new ParserService();

        $controller = new ParserController(
            validatorService: $validatorService,
            parserService: $parserService
        );

        $html = '<!DOCTYPE html><html><body><p>Hi</p></body></html>';

        $result = $controller->handleRequest(
            data: [
                'context' => $html,
                'type' => 'html',
                'renderingType' => 'html'
            ]
        );

        $rendered = $result->render();

        $this->assertInstanceOf(
            HtmlParserView::class,
            $result
        );
        $this->assertStringContainsString(
            'Errors:',
            $rendered
        );
    }

    public function test_handle_request_with_valid_html(): void
    {
        $validatorService = new ValidatorService();
        $parserService = new ParserService();

        $controller = new ParserController(
            validatorService: $validatorService,
            parserService: $parserService
        );

        $html = '<!DOCTYPE html><html lang="en"><head><title>Ok</title></head><body><p>Hello</p></body></html>';

        $result = $controller->handleRequest(
            data: [
                'context' => $html,
                'type' => 'html',
                'renderingType' => 'html',
            ]
        );

        $rendered = $result->render();

        $this->assertInstanceOf(
            HtmlParserView::class,
            $result
        );
        $this->assertStringContainsString(
            'Valid: yes',
            $rendered
        );
        $this->assertStringContainsString(
            'Hello',
            $rendered
        );
    }

    public function test_handle_request_stub_markdown_rendering_json(): void
    {
        $validatorService = new ValidatorService();
        $parserService = new ParserService();

        $controller = new ParserController(
            validatorService: $validatorService,
            parserService: $parserService
        );

        $markdown = '# Example Title';

        $expectedJsonRender = [
            'Validation' => [
                'Valid' => 'yes',
                'Errors' => [],
                'Warnings' => [],
            ],
            'Parsed' => [
                'Name' => 'root',
                'Children' => [
                    [
                        'Name' => 'markdown',
                        'Content' => $markdown,
                    ]
                ]
            ]
        ];

        $encodedJsonRender = json_encode(
            value: $expectedJsonRender,
            flags: JSON_PRETTY_PRINT,
        );

        $result = $controller->handleRequest(
            data: [
                'context' => $markdown,
                'type' => 'markdown',
                'renderingType' => 'json',
            ]
        );

        $rendered = $result->render();

        $this->assertInstanceOf(
            JsonParserView::class,
            $result
        );
        $this->assertStringContainsString(
            "\"Valid\": \"yes\"",
            $rendered
        );
        $this->assertIsString($encodedJsonRender);
        $this->assertJsonStringEqualsJsonString(
            $encodedJsonRender,
            $rendered
        );
    }

    public function test_handle_request_stub_markdown_html_rendering(): void
    {
        $validatorService = new ValidatorService();
        $parserService = new ParserService();

        $controller = new ParserController(
            validatorService: $validatorService,
            parserService: $parserService
        );

        $markdown = '# Example Title';

        $result = $controller->handleRequest(
            data: [
                'context' => $markdown,
                'type' => 'markdown',
                'renderingType' => 'html',
            ]
        );

        $rendered = $result->render();

        $this->assertInstanceOf(
            HtmlParserView::class,
            $result
        );
        $this->assertStringContainsString(
            'Valid: yes',
            $rendered
        );
        $this->assertStringContainsString(
            'Example Title',
            $rendered
        );
    }

    public function test_error_while_opening_uploaded_file(): void
    {
        $validatorServiceMock = $this->createMock(ValidatorService::class);
        $parserServiceMock = $this->createMock(ParserService::class);

        $parserController = new ParserController(
            validatorService: $validatorServiceMock,
            parserService: $parserServiceMock
        );

        $data = [
            'context' => '',
            'type' => 'html',
            'renderingType' => 'json',
        ];

        $_FILES = [
            'file' => [
                'name' => 'inexistent_file.html',
                'type' => 'text/html',
                'tmp_name' => __DIR__ . '/../../../fixtures/tests/inexistent_file.html',
                'error' => 0,
                'size' => 999,
            ]
        ];

        $expected = [
            'Validation' => [
                'Valid' => 'no',
                'Errors' => [
                    [
                        'message' => 'Error while opening the uploaded file'
                    ]
                ],
                'Warnings' => [],
            ],
            'Parsed' => [],
        ];

        $response = $parserController->handleRequest(data: $data);

        $this->assertEquals(json_encode($expected, JSON_PRETTY_PRINT), $response->render());

        $_FILES = [];
    }

    public function test_file_with_wrong_extension(): void
    {
        $validatorServiceMock = $this->createMock(ValidatorService::class);
        $parserServiceMock = $this->createMock(ParserService::class);

        $parserController = new ParserController(
            validatorService: $validatorServiceMock,
            parserService: $parserServiceMock
        );

        $data = [
            'context' => '',
            'type' => 'html',
            'renderingType' => 'json',
        ];

        $_FILES = [
            'file' => [
                'name' => 'invalid_html_format.c',
                'type' => 'text/html',
                'tmp_name' => __DIR__ . '/../../../fixtures/tests/invalid_html_format.c',
                'error' => 0,
                'size' => 999,
            ]
        ];

        $expected = [
            'Validation' => [
                'Valid' => 'no',
                'Errors' => [
                    [
                        'message' => 'File uploaded has the wrong extension'
                    ]
                ],
                'Warnings' => [],
            ],
            'Parsed' => [],
        ];

        $response = $parserController->handleRequest(data: $data);

        $this->assertEquals(json_encode($expected, JSON_PRETTY_PRINT), $response->render());

        $_FILES = [];
    }

    public function test_html_and_markdown_round_trip(): void
    {
        // --- HTML ---
        $htmlPost = [
            'context' => '<!DOCTYPE html><html lang="en"><head><title>Ok</title></head><body><p>Hello</p></body></html>',
            'type' => 'html',
            'renderingType' => 'json',
        ];

        $validatorService = new ValidatorService();
        $parserService = new ParserService();

        $controller = new ParserController(
            validatorService: $validatorService,
            parserService: $parserService
        );

        $htmlView = $controller->handleRequest(data: $htmlPost);

        $rendered1 = $htmlView->render();

        $this->assertStringContainsString(
            'Ok',
            $rendered1
        );
        $this->assertStringContainsString(
            'Hello',
            $rendered1
        );

        // --- Markdown ---
        $markdownPost = [
            'context' => '# Heading',
            'type' => 'markdown',
            'renderingType' => 'json',
        ];

        $markdownView = $controller->handleRequest(data: $markdownPost);

        $rendered2 = $markdownView->render();

        $this->assertStringContainsString(
            'markdown',
            $rendered2
        );
        $this->assertStringContainsString(
            '# Heading',
            $rendered2
        );
    }
}

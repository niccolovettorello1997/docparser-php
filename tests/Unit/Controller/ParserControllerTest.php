<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Controller;

use Niccolo\DocparserPhp\Controller\ParserController;
use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\View\ElementValidationResultView;
use Niccolo\DocparserPhp\View\Parser\HtmlParserView;
use Niccolo\DocparserPhp\View\Parser\JsonParserView;
use PHPUnit\Framework\TestCase;

class ParserControllerTest extends TestCase
{
    public function test_handle_request_with_unsupported_input_type(): void
    {
        $controller = new ParserController();

        // Unsupported type
        $result = $controller->handleRequest(
            data: [
                'context' => '<html></html>',
                'type' => 'xml',
                'renderingType' => 'html',
            ]
        );

        $rendered = $result[0]->render();

        $this->assertCount(
            1,
            $result
        );
        $this->assertInstanceOf(
            ElementValidationResultView::class,
            $result[0]
        );
        $this->assertStringContainsString(
            'Unsupported input type: xml',
            $rendered
        );
    }

    public function test_handle_request_with_unsupported_rendering_type(): void
    {
        $controller = new ParserController();

        // Unsupported type
        $result = $controller->handleRequest(
            data: [
                'context' => '<html></html>',
                'type' => 'html',
                'renderingType' => 'xml',
            ]
        );

        $rendered = $result[0]->render();

        $this->assertCount(
            1,
            $result
        );
        $this->assertInstanceOf(
            ElementValidationResultView::class,
            $result[0]
        );
        $this->assertStringContainsString(
            'Unsupported rendering type: xml',
            $rendered
        );
    }

    public function test_handle_request_html_with_empty_context(): void
    {
        $controller = new ParserController();

        // Empty context
        $result = $controller->handleRequest(
            data: [
                'context' => '',
                'type' => 'html',
                'renderingType' => 'html',
            ]
        );

        $rendered = $result[0]->render();

        $this->assertCount(
            1,
            $result
        );
        $this->assertInstanceOf(
            ElementValidationResultView::class,
            $result[0]
        );
        $this->assertStringContainsString(
            'No context provided',
            $rendered
        );
    }

    public function test_handle_request_markdown_with_empty_context(): void
    {
        $controller = new ParserController();

        // Empty context
        $result = $controller->handleRequest(
            data: [
                'context' => '',
                'type' => 'markdown',
                'renderingType' => 'html',
            ]
        );

        $rendered = $result[0]->render();

        $this->assertCount(
            1,
            $result
        );
        $this->assertInstanceOf(
            ElementValidationResultView::class,
            $result[0]
        );
        $this->assertStringContainsString(
            'No context provided',
            $rendered
        );
    }

    public function test_handle_request_with_invalid_html(): void
    {
        $controller = new ParserController();

        $html = '<!DOCTYPE html><html><body><p>Hi</p></body></html>';

        $result = $controller->handleRequest(
            data: [
                'context' => $html,
                'type' => 'html',
                'renderingType' => 'html'
            ]
        );

        $rendered = $result[0]->render();

        // Only validation view present
        $this->assertCount(
            1,
            $result
        );
        $this->assertInstanceOf(
            ElementValidationResultView::class,
            $result[0]
        );
        $this->assertStringContainsString(
            'Errors:',
            $rendered
        );
    }

    public function test_handle_request_with_valid_html(): void
    {
        $controller = new ParserController();

        $html = '<!DOCTYPE html><html lang="en"><head><title>Ok</title></head><body><p>Hello</p></body></html>';

        $result = $controller->handleRequest(
            data: [
                'context' => $html,
                'type' => 'html',
                'renderingType' => 'html',
            ]
        );

        // Validation and parsing views present
        $this->assertCount(
            2,
            $result
        );
        $this->assertInstanceOf(
            ElementValidationResultView::class,
            $result[0]
        );
        $this->assertInstanceOf(
            HtmlParserView::class,
            $result[1]
        );
        $this->assertStringContainsString(
            'Your content is valid!',
            $result[0]->render()
        );
        $this->assertStringContainsString(
            'Hello',
            $result[1]->render()
        );
    }

    public function test_get_json_result_success(): void
    {
        $controller = new ParserController();

        $node = new Node(
            tagName: 'p',
            content: 'Hello',
            attributes: [],
            children: []
        );

        $view = new JsonParserView(tree: $node);

        $response = $controller->getJsonResult(views: [$view]);

        $this->assertSame(
            200,
            $response->getStatusCode()
        );
        $this->assertJson($response->getContent());
        $this->assertStringContainsString(
            'Hello',
            $response->getContent()
        );
    }

    public function test_get_json_result_error(): void
    {
        $controller = new ParserController();

        $response = $controller->getJsonResult(views: []);

	/** @var array<mixed> $decodedContent */
	$decodedContent = json_decode(json: $response->getContent(), associative: true);

        $this->assertSame(
            500,
            $response->getStatusCode()
        );
        $this->assertArrayHasKey(
	    'error',
	    $decodedContent
        );
    }

    public function test_handle_request_stub_markdown_rendering_json(): void
    {
        $controller = new ParserController();

        $markdown = '# Example Title';

        $expectedJsonRender = [
            'Name' => 'root',
            'Children' => [
                [
                    'Name' => 'markdown',
                    'Content' => $markdown,
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

        // Validation and parsing views present
        $this->assertCount(
            2,
            $result
        );
        $this->assertInstanceOf(
            ElementValidationResultView::class,
            $result[0]
        );
        $this->assertInstanceOf(
            JsonParserView::class,
            $result[1]
        );
        $this->assertStringContainsString(
            'Your content is valid!',
            $result[0]->render()
        );
	$this->assertIsString($encodedJsonRender);
	$this->assertJsonStringEqualsJsonString(
	    $encodedJsonRender,
            $result[1]->render()
        );
    }

    public function test_handle_request_stub_markdown_html_rendering(): void
    {
        $controller = new ParserController();

        $markdown = '# Example Title';

        $result = $controller->handleRequest(
            data: [
                'context' => $markdown,
                'type' => 'markdown',
                'renderingType' => 'html',
            ]
        );

        // Validation and parsing views present
        $this->assertCount(
            2,
            $result
        );
        $this->assertInstanceOf(
            ElementValidationResultView::class,
            $result[0]
        );
        $this->assertInstanceOf(
            HtmlParserView::class,
            $result[1]
        );
        $this->assertStringContainsString(
            'Your content is valid!',
            $result[0]->render()
        );
        $this->assertStringContainsString(
            'Example Title',
            $result[1]->render()
        );
    }

    public function test_html_and_markdown_round_trip(): void
    {
        // --- HTML ---
        $htmlPost = [
            'context' => '<!DOCTYPE html><html lang="en"><head><title>Ok</title></head><body><p>Hello</p></body></html>',
            'type' => 'html',
            'renderingType' => 'json',
        ];

        $controller = new ParserController();

        $views = $controller->handleRequest(data: $htmlPost);

        $resultHtml = $controller->getJsonResult(views: $views);

        $this->assertStringContainsString(
            'Ok',
            $resultHtml->getContent()
        );
        $this->assertStringContainsString(
            'Hello',
            $resultHtml->getContent()
        );

        // --- Markdown ---
        $markdownPost = [
            'context' => '# Heading',
            'type' => 'markdown',
            'renderingType' => 'json',
        ];

        $markdownViews = $controller->handleRequest(data: $markdownPost);

        $resultMarkdown = $controller->getJsonResult(views: $markdownViews);

        $this->assertStringContainsString(
            'markdown',
            $resultMarkdown->getContent()
        );
        $this->assertStringContainsString(
            '# Heading',
            $resultMarkdown->getContent()
        );
    }
}

<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\View\Parser\HtmlParserView;
use Niccolo\DocparserPhp\View\Parser\JsonParserView;
use Niccolo\DocparserPhp\Controller\ParserController;
use Niccolo\DocparserPhp\View\ElementValidationResultView;

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
            expectedCount: 1,
            haystack: $result
        );
        $this->assertInstanceOf(
            expected: ElementValidationResultView::class,
            actual: $result[0]
        );
        $this->assertStringContainsString(
            needle: 'Unsupported input type: xml',
            haystack: $rendered
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
            expectedCount: 1,
            haystack: $result
        );
        $this->assertInstanceOf(
            expected: ElementValidationResultView::class,
            actual: $result[0]
        );
        $this->assertStringContainsString(
            needle: 'Unsupported rendering type: xml',
            haystack: $rendered
        );
    }

    public function test_handle_request_with_empty_context(): void
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
            expectedCount: 1,
            haystack: $result
        );
        $this->assertInstanceOf(
            expected: ElementValidationResultView::class,
            actual: $result[0]
        );
        $this->assertStringContainsString(
            needle: 'No context provided',
            haystack: $rendered
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
            expectedCount: 1,
            haystack: $result
        );
        $this->assertInstanceOf(
            expected: ElementValidationResultView::class,
            actual: $result[0]
        );
        $this->assertStringContainsString(
            needle: 'Errors:',
            haystack: $rendered
        );
    }

    public function test_handle_request_with_valid_html(): void
    {
        $controller = new ParserController();

        $html = '<!DOCTYPE html><html lang="en"><head><title>Ok</title></head><body><p>Hello</p></body></html>';

        $result = $controller->handleRequest([
            'context' => $html,
            'type' => 'html',
            'renderingType' => 'html',
        ]);

        // Validation and parsing views present
        $this->assertCount(
            expectedCount: 2,
            haystack: $result
        );
        $this->assertInstanceOf(
            expected: ElementValidationResultView::class,
            actual: $result[0]
        );
        $this->assertInstanceOf(
            expected: HtmlParserView::class,
            actual: $result[1]
        );
        $this->assertStringContainsString(
            needle: 'Your content is valid!',
            haystack: $result[0]->render()
        );
        $this->assertStringContainsString(
            needle: 'Hello',
            haystack: $result[1]->render()
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
            expected: 200,
            actual: $response->getStatusCode()
        );
        $this->assertJson(actual: $response->getContent());
        $this->assertStringContainsString(
            needle: 'Hello',
            haystack: $response->getContent()
        );
    }

    public function test_get_json_result_error(): void
    {
        $controller = new ParserController();

        $response = $controller->getJsonResult(views: []);

        $this->assertSame(
            expected: 500,
            actual: $response->getStatusCode()
        );
        $this->assertArrayHasKey(
            key: 'error',
            array: json_decode(json: $response->getContent(), associative: true)
        );
    }
}

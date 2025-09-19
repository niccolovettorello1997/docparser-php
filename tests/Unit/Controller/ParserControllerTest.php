<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Controller\ParserController;
use Niccolo\DocparserPhp\View\ElementValidationResultView;
use Niccolo\DocparserPhp\View\Parser\HtmlParserView;

class ParserControllerTest extends TestCase
{
    public function test_handle_request_with_unsupported_type(): void
    {
        $controller = new ParserController();

        // Unsupported type
        $result = $controller->handleRequest(
            data: [
                'context' => '<html></html>',
                'type' => 'xml',
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
            needle: 'Unsupported type: xml',
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
            needle: 'No context provided.',
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
}

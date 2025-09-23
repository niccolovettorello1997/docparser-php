<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\View;

use Niccolo\DocparserPhp\Model\Core\Parser\ParserComponent;
use Niccolo\DocparserPhp\View\Parser\HtmlParserView;
use PHPUnit\Framework\TestCase;

class HtmlParserViewTest extends TestCase
{
    public function test_view_html_parsed_tree(): void
    {
        $html = file_get_contents(filename: __DIR__ . "/../../../fixtures/tests/valid_html.html");

        $parserComponent = ParserComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../config/Parser/parser_html.yaml"
        );

        $tree = $parserComponent->run();

        $htmlParserView = new HtmlParserView(
            tree: $tree
        );

        $result = $htmlParserView->render();

        $this->assertStringContainsString(
            needle: '<li><strong>Element name -> </strong>title</li>',
            haystack: $result,
        );
        $this->assertStringContainsString(
            needle: '<li><strong>Element content -> </strong>Example Document</li>',
            haystack: $result
        );
        $this->assertStringContainsString(
            needle: '<li><strong>Element name -> </strong>p</li>',
            haystack: $result,
        );
        $this->assertStringContainsString(
            needle: '<li><strong>Element content -> </strong>This is the first section of the page.</li>',
            haystack: $result
        );
        $this->assertStringContainsString(
            needle: '<li><strong>Element name -> </strong>h1</li>',
            haystack: $result,
        );
        $this->assertStringContainsString(
            needle: '<li><strong>Element content -> </strong>Welcome to My Page</li>',
            haystack: $result
        );
    }
}

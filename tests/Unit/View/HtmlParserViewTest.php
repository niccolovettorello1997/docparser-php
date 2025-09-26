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
            needle: '<li>0: <ul><li>Name: title</li><li>Content: Example Document</li></ul></li>',
            haystack: $result,
        );
        $this->assertStringContainsString(
            needle: '<li>0: <ul><li>Name: p</li><li>Content: This is the first section of the page.</li></ul></li>',
            haystack: $result,
        );
        $this->assertStringContainsString(
            needle: '<li>0: <ul><li>Name: h1</li><li>Content: Welcome to My Page</li></ul></li>',
            haystack: $result,
        );
    }
}

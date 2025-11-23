<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Unit\View;

use DocparserPhp\Model\Core\Parser\ParserComponent;
use DocparserPhp\View\HtmlParserView;
use PHPUnit\Framework\TestCase;

class HtmlParserViewTest extends TestCase
{
    public function test_view_html_parsed_tree(): void
    {
        $html = file_get_contents(filename: __DIR__ . "/../../../fixtures/tests/valid_html.html");

        if (false === $html) {
            $this->fail('Failed to read the HTML fixture file.');
        }

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
            '<li>0: <ul><li>Name: title</li><li>Content: Example Document</li></ul></li>',
            $result,
        );
        $this->assertStringContainsString(
            '<li>0: <ul><li>Name: p</li><li>Content: This is the first section of the page.</li></ul></li>',
            $result,
        );
        $this->assertStringContainsString(
            '<li>0: <ul><li>Name: h1</li><li>Content: Welcome to My Page</li></ul></li>',
            $result,
        );
    }
}

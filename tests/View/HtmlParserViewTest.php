<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\View;

use Niccolo\DocparserPhp\Model\Core\Parser\ParserComponent;
use Niccolo\DocparserPhp\View\Parser\HtmlParserView;
use PHPUnit\Framework\TestCase;

class HtmlParserViewTest extends TestCase
{
    public function test_view_html_parsed_tree(): void
    {
        $expectedTitleRendering = "<div><strong>Title => </strong>Example Document</div>";
        $expectedHeadingRendering = "<div><strong>Paragraphs: </strong><ul><li>This is the first section of the page.</li><li>This is the second section. Notice that headings, paragraphs, and links are all valid here.</li><li>&copy; 2025 Example Company</li></ul></div>";
        $expectedParagraphRendering = "<div><strong>Headings: </strong><ul><li>Welcome to My Page</li><li>Section 1</li><li>Section 2</li></ul></div>";
        $html = file_get_contents(filename: __DIR__ . "/../../fixtures/tests/valid_html.html");
        $parserComponent = ParserComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../config/Parser/parser_html.yaml"
        );

        $abstractParsers = $parserComponent->run();

        $htmlParserView = new HtmlParserView(
            doctypeParser: $abstractParsers[0]
        );

        $result = $htmlParserView->render();

        $this->assertEquals(
            expected: $expectedTitleRendering . $expectedHeadingRendering . $expectedParagraphRendering,
            actual: $result,
        );
    }
}

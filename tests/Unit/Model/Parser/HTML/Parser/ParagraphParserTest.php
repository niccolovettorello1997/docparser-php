<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Parser\HTML\Parser;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\ParagraphParser;

class ParagraphParserTest extends TestCase
{
    public function test_parse_paragraphs(): void
    {
        $firstParagraphContent = 'This is the first section of the page.';
        $secondParagraphContent = 'This is the second section. Notice that headings, paragraphs, and links are all valid here.';
        $thirdParagraphContent = '&copy; 2025 Example Company';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../../fixtures/tests/valid_html.html");
        $sharedContext = new SharedContext(context: $html);

        $paragraphs = ParagraphParser::parse(context: $sharedContext);

        $this->assertCount(
            expectedCount: 3,
            haystack: $paragraphs
        );
        $this->assertEquals(
            expected: $firstParagraphContent,
            actual: $paragraphs[0]->getContent()
        );
        $this->assertEmpty(
            actual: $paragraphs[0]->getChildren()
        );
        $this->assertEquals(
            expected: $secondParagraphContent,
            actual: $paragraphs[1]->getContent()
        );
        $this->assertEmpty(
            actual: $paragraphs[1]->getChildren()
        );
        $this->assertEquals(
            expected: $thirdParagraphContent,
            actual: $paragraphs[2]->getContent()
        );
        $this->assertEmpty(
            actual: $paragraphs[2]->getChildren()
        );
    }
}

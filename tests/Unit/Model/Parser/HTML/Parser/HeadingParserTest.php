<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Parser\HTML\Parser;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\HeadingParser;

class HeadingParserTest extends TestCase
{
    public function test_parse_headings(): void
    {
        $firstHeadingContent = 'Welcome to My Page';
        $secondHeadingContent = 'Section 1';
        $thirdHeadingContent = 'Section 2';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../../fixtures/tests/valid_html.html");
        $sharedContext = new SharedContext(context: $html);

        $headings = HeadingParser::parse(context: $sharedContext);

        $this->assertCount(
            expectedCount: 3,
            haystack: $headings
        );
        $this->assertEquals(
            expected: $firstHeadingContent,
            actual: $headings[0]->getContent()
        );
        $this->assertEmpty(
            actual: $headings[0]->getChildren()
        );
        $this->assertEquals(
            expected: $secondHeadingContent,
            actual: $headings[1]->getContent()
        );
        $this->assertEmpty(
            actual: $headings[1]->getChildren()
        );
        $this->assertEquals(
            expected: $thirdHeadingContent,
            actual: $headings[2]->getContent()
        );
        $this->assertEmpty(
            actual: $headings[2]->getChildren()
        );
    }
}

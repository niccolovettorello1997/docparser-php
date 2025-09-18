<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Parser\HTML\Parser;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\TitleParser;

class TitleParserTest extends TestCase
{
    public function test_parse_title(): void
    {
        $titleContent = 'Example Document';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../../fixtures/tests/valid_html.html");
        $sharedContext = new SharedContext(context: $html);

        $title = TitleParser::parse(context: $sharedContext);

        $this->assertCount(
            expectedCount: 1,
            haystack: $title
        );
        $this->assertEquals(
            expected: $titleContent,
            actual: $title[0]->getContent()
        );
        $this->assertEmpty(
            actual: $title[0]->getChildren()
        );
    }
}

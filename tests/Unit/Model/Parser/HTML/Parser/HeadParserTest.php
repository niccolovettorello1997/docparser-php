<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Parser\HTML\Parser;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\HeadParser;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\TitleParser;

class HeadParserTest extends TestCase
{
    public function test_parse_head(): void
    {
        $headContent = <<<HTML

          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Example Document</title>
          <meta name="description" content="A simple example of a well-structured HTML5 document.">

        HTML;

        $html = file_get_contents(filename: __DIR__ . "/../../../../../../fixtures/tests/valid_html.html");
        $sharedContext = new SharedContext(context: $html);

        $head = HeadParser::parse(context: $sharedContext);

        $this->assertCount(
            expectedCount: 1,
            haystack: $head
        );
        $this->assertEquals(
            expected: $headContent,
            actual: $head[0]->getContent()
        );
        $this->assertCount(
            expectedCount:1,
            haystack: $head[0]->getChildren()
        );
        $this->assertInstanceOf(
            expected: TitleParser::class,
            actual: $head[0]->getChildren()[0]
        );
    }
}

<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Parser\HTML\Parser;

use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;
use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\HeadParser;

class HeadParserTest extends TestCase
{
    public function test_parse_head(): void
    {
        $html = <<<HTML

        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Example Document</title>
          <meta name="description" content="A simple example of a well-structured HTML5 document.">
        </head>
        <body>
        </body>

        HTML;

        $headParser = new HeadParser();
        $headNode = $headParser->parse(content: $html);

        $this->assertNotNull(actual: $headNode);
        $this->assertNull(actual: $headNode->getContent());
        $this->assertEquals(
          expected: HtmlElementType::HEAD->value,
          actual: $headNode->getTagName()
        );
    }
}

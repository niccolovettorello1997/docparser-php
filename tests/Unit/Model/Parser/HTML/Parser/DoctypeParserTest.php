<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Parser\HTML\Parser;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\DoctypeParser;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;

class DoctypeParserTest extends TestCase
{
    public function test_parse_doctype(): void
    {
        $html = file_get_contents(filename: __DIR__ . "/../../../../../../fixtures/tests/valid_html.html");

        $doctypeParser = new DoctypeParser();
        $doctypeNode = $doctypeParser->parse(content: $html);

        $this->assertNotNull(actual: $doctypeNode);
        $this->assertNull(actual: $doctypeNode->getContent());
        $this->assertCount(
            expectedCount:1,
            haystack: $doctypeNode->getChildren()
        );
        $this->assertEquals(
            expected: HtmlElementType::HTML->value,
            actual: $doctypeNode->getChildren()[0]->getTagName()
        );
    }
}

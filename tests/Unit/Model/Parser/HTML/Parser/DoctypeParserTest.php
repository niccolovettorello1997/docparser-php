<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Parser\HTML\Parser;

use Niccolo\DocparserPhp\Model\Parser\HTML\Element\DoctypeParser;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;
use PHPUnit\Framework\TestCase;

class DoctypeParserTest extends TestCase
{
    public function test_parse_doctype(): void
    {
        $html = file_get_contents(filename: __DIR__ . "/../../../../../../fixtures/tests/valid_html.html");

        if (false === $html) {
            $this->fail('Failed to read the HTML fixture file.');
        }

        $doctypeParser = new DoctypeParser();
        $doctypeNode = $doctypeParser->parse(content: $html);

        $this->assertNotNull($doctypeNode);
        $this->assertNull($doctypeNode->getContent());
        $this->assertCount(
            1,
            $doctypeNode->getChildren()
        );
        $this->assertEquals(
            HtmlElementType::HTML->value,
            $doctypeNode->getChildren()[0]->getTagName()
        );
    }
}

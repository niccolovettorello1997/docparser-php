<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Core\Parser;

use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;
use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Core\Parser\ParserAdapter;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\DoctypeParser;

class ParserAdapterTest extends TestCase
{
    public function test_parse(): void
    {
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/valid_html.html");
        $sharedContext = new SharedContext(context: $html);
        $parserAdapter = new ParserAdapter(
            rootElements: [
                DoctypeParser::class
            ]
        );

        $result = $parserAdapter->parse(sharedContext: $sharedContext);

        $this->assertNotNull(actual: $result);
        $this->assertEquals(
            expected: HtmlElementType::DOCTYPE->value,
            actual: $result->getChildren()[0]->getTagName(),
        );
        $this->assertNull(actual: $result->getChildren()[0]->getContent());
        $this->assertCount(
            expectedCount:1,
            haystack: $result->getChildren()[0]->getChildren()
        );
        $this->assertEquals(
            expected: HtmlElementType::HTML->value,
            actual: $result->getChildren()[0]->getChildren()[0]->getTagName()
        );
    }
}

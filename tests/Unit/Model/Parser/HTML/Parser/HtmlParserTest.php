<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Parser\HTML\Parser;

use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\HtmlParser;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;
use PHPUnit\Framework\TestCase;

class HtmlParserTest extends TestCase
{
    public function test_parse_html(): void
    {
        $html = file_get_contents(filename: __DIR__ . "/../../../../../../fixtures/tests/valid_html.html");

        $htmlParser = new HtmlParser();
        $htmlNode = $htmlParser->parse(content: $html);

        $this->assertNotNull(actual: $htmlNode);
        $this->assertEquals(
            expected: HtmlElementType::HTML->value,
            actual: $htmlNode->getTagName()
        );
        $this->assertNull(actual: $htmlNode->getContent());
        $this->assertCount(
            expectedCount:2,
            haystack: $htmlNode->getChildren()
        );
        $this->assertCount(
            expectedCount:1,
            haystack: array_filter(
                array: $htmlNode->getChildren(),
                callback: fn (Node $node): bool => $node->getTagName() === HtmlElementType::HEAD->value
            )
        );
        $this->assertCount(
            expectedCount:1,
            haystack: array_filter(
                array: $htmlNode->getChildren(),
                callback: fn (Node $node): bool => $node->getTagName() === HtmlElementType::BODY->value
            )
        );
        $this->assertArrayHasKey(
            key: 'lang',
            array: $htmlNode->getAttributes()
        );
        $this->assertEquals(
            expected: 'en',
            actual: $htmlNode->getAttributes()['lang']
        );
    }
}

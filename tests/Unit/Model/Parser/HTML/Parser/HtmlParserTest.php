<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Unit\Model\Parser\HTML\Parser;

use DocparserPhp\Model\Core\Parser\Node;
use DocparserPhp\Model\Parser\HTML\Element\HtmlParser;
use DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;
use PHPUnit\Framework\TestCase;

class HtmlParserTest extends TestCase
{
    public function test_parse_html(): void
    {
        $html = file_get_contents(filename: __DIR__ . "/../../../../../../fixtures/tests/valid_html.html");

        if (false === $html) {
            $this->fail('Failed to read the HTML fixture file.');
        }

        $htmlParser = new HtmlParser();
        $htmlNode = $htmlParser->parse(content: $html);

        $this->assertNotNull($htmlNode);
        $this->assertEquals(
            HtmlElementType::HTML->value,
            $htmlNode->getTagName()
        );
        $this->assertNull($htmlNode->getContent());
        $this->assertCount(
            2,
            $htmlNode->getChildren()
        );
        $this->assertCount(
            1,
            array_filter(
                array: $htmlNode->getChildren(),
                callback: fn (Node $node): bool => $node->getTagName() === HtmlElementType::HEAD->value
            )
        );
        $this->assertCount(
            1,
            array_filter(
                array: $htmlNode->getChildren(),
                callback: fn (Node $node): bool => $node->getTagName() === HtmlElementType::BODY->value
            )
        );
        $this->assertArrayHasKey(
            'lang',
            $htmlNode->getAttributes()
        );
        $this->assertEquals(
            'en',
            $htmlNode->getAttributes()['lang']
        );
    }
}

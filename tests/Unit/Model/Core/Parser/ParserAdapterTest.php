<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Core\Parser;

use Niccolo\DocparserPhp\Model\Core\Parser\ParserAdapter;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\DoctypeParser;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;
use PHPUnit\Framework\TestCase;

class ParserAdapterTest extends TestCase
{
    public function test_parse(): void
    {
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/valid_html.html");

        if (false === $html) {
            $this->fail('Failed to read the HTML fixture file.');
        }

        $sharedContext = new SharedContext(context: $html);
        $parserAdapter = new ParserAdapter(
            rootElements: [
                DoctypeParser::class
            ]
        );

        $result = $parserAdapter->parse(sharedContext: $sharedContext);

        $this->assertNotNull($result);
        $this->assertEquals(
            HtmlElementType::DOCTYPE->value,
            $result->getChildren()[0]->getTagName(),
        );
        $this->assertNull($result->getChildren()[0]->getContent());
        $this->assertCount(
            1,
            $result->getChildren()[0]->getChildren()
        );
        $this->assertEquals(
            HtmlElementType::HTML->value,
            $result->getChildren()[0]->getChildren()[0]->getTagName()
        );
    }
}

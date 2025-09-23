<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Core\Parser;

use Niccolo\DocparserPhp\Model\Core\Parser\ParserComponent;
use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;

class ParserComponentTest extends TestCase
{
    public function test_parse(): void
    {
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/valid_html.html");
        $parserComponent = ParserComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Parser/parser_html.yaml"
        );

        $result = $parserComponent->run();

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

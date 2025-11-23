<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Unit\Model\Core\Parser;

use DocparserPhp\Model\Core\Parser\ParserComponent;
use DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;
use PHPUnit\Framework\TestCase;

class ParserComponentTest extends TestCase
{
    public function test_parse(): void
    {
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/valid_html.html");

        if (false === $html) {
            $this->fail('Failed to read the HTML fixture file.');
        }

        $parserComponent = ParserComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Parser/parser_html.yaml"
        );

        $result = $parserComponent->run();

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

<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Parser\HTML\Parser;

use Niccolo\DocparserPhp\Model\Parser\HTML\Element\TitleParser;
use PHPUnit\Framework\TestCase;

class TitleParserTest extends TestCase
{
    public function test_parse_title(): void
    {
        $titleContent = 'Example Document';

        $html = <<<HTML

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Example Document</title>
        <meta name="description" content="A simple example of a well-structured HTML5 document.">

        HTML;

        $titleParser = new TitleParser();
        $titleNode = $titleParser->parse(content: $html);

        $this->assertNotNull($titleNode);
        $this->assertEquals(
            $titleContent,
            $titleNode->getContent()
        );
        $this->assertEmpty(
            $titleNode->getChildren()
        );
    }
}

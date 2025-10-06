<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Parser\HTML\Parser;

use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\ParagraphParser;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;
use PHPUnit\Framework\TestCase;

class ParagraphParserTest extends TestCase
{
    public function test_parse_paragraphs(): void
    {
        $firstParagraphContent = 'This is the first section of the page.';
        $secondParagraphContent = 'This is the second section. Notice that headings, paragraphs, and links are all valid here.';
        $thirdParagraphContent = '&copy; 2025 Example Company';

        $body = <<<BODY

        <header>
          <h1>Welcome to My Page</h1>
          <nav>
            <ul>
              <li><a href="http://www.example1.com">example 1</a></li>
              <li><a href="http://www.example2.com">example 2</a></li>
            </ul>
          </nav>
        </header>

        <main>
        <section id="section1">
            <h2>Section 1</h2>
            <p>This is the first section of the page.</p>
        </section>

        <section id="section2">
            <h2>Section 2</h2>
            <p>This is the second section. Notice that headings, paragraphs, and links are all valid here.</p>
        </section>
        </main>

        <footer>
            <p>&copy; 2025 Example Company</p>
        </footer>

        BODY;

        $paragraphsParser = new ParagraphParser();
        $paragraphNode = $paragraphsParser->parse(content: $body);

        $this->assertNotNull($paragraphNode);
        $this->assertEquals(
            HtmlElementType::PARAGRAPHS->value,
            $paragraphNode->getTagName()
        );
        $this->assertCount(
            3,
            $paragraphNode->getChildren()
        );
        $this->assertCount(
            1,
            array_filter(
                array: $paragraphNode->getChildren(),
                callback: fn (Node $node): bool => $node->getContent() === $firstParagraphContent
            )
        );
        $this->assertCount(
            1,
            array_filter(
                array: $paragraphNode->getChildren(),
                callback: fn (Node $node): bool => $node->getContent() === $secondParagraphContent
            )
        );
        $this->assertCount(
            1,
            array_filter(
                array: $paragraphNode->getChildren(),
                callback: fn (Node $node): bool => $node->getContent() === $thirdParagraphContent
            )
        );
    }
}

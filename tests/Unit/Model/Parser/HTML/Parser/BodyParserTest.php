<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Parser\HTML\Parser;

use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\BodyParser;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;
use PHPUnit\Framework\TestCase;

class BodyParserTest extends TestCase
{
    public function test_parse_body(): void
    {
        $content = <<<HTML

        <head>
          <title>Example Document</title>
        </head>
        <body>
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
        </body>

        HTML;

        $bodyParser = new BodyParser();
        $bodyNode = $bodyParser->parse(content: $content);

        $this->assertNotNull(actual: $bodyNode);
        $this->assertEquals(
            expected: HtmlElementType::BODY->value,
            actual: $bodyNode->getTagName()
        );
        $this->assertNull(actual: $bodyNode->getContent());
        $this->assertCount(
            expectedCount:2,
            haystack: $bodyNode->getChildren()
        );
        $this->assertCount(
            expectedCount:1,
            haystack: array_filter(
                array: $bodyNode->getChildren(),
                callback: fn (Node $node): bool => $node->getTagName() === HtmlElementType::HEADINGS->value
            )
        );
        $this->assertCount(
            expectedCount:1,
            haystack: array_filter(
                array: $bodyNode->getChildren(),
                callback: fn (Node $node): bool => $node->getTagName() === HtmlElementType::PARAGRAPHS->value
            )
        );
    }
}

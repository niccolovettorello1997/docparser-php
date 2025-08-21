<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Model\Parser\HTML\Parser;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Core\Parser\AbstractParser;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\BodyParser;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\HeadingParser;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\ParagraphParser;

class BodyParserTest extends TestCase
{
    public function test_parse_body(): void
    {
        $bodyContent = <<<HTML

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

        HTML;

        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/valid_html.html");
        $sharedContext = new SharedContext(context: $html);

        $body = BodyParser::parse(context: $sharedContext);

        $this->assertCount(
            expectedCount: 1,
            haystack: $body
        );
        $this->assertEquals(
            expected: $bodyContent,
            actual: $body[0]->getContent()
        );
        $this->assertCount(
            expectedCount:6,
            haystack: $body[0]->getChildren()
        );
        $this->assertCount(
            expectedCount:3,
            haystack: array_filter(
                array: $body[0]->getChildren(),
                callback: fn (AbstractParser $parser): bool => $parser instanceof ParagraphParser
            )
        );
        $this->assertCount(
            expectedCount:3,
            haystack: array_filter(
                array: $body[0]->getChildren(),
                callback: fn (AbstractParser $parser): bool => $parser instanceof HeadingParser
            )
        );
    }
}

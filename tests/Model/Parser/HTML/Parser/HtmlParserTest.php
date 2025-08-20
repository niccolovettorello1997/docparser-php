<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Model\Parser\HTML\Parser;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Core\Parser\AbstractParser;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\BodyParser;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\HeadParser;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\HtmlParser;

class HtmlParserTest extends TestCase
{
    public function test_parse_html(): void
    {
        $htmlContent = <<<HTML

        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Example Document</title>
          <meta name="description" content="A simple example of a well-structured HTML5 document.">
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

        $htmlContext = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/valid_html.html");
        $sharedContext = new SharedContext(context: $htmlContext);

        $html = HtmlParser::parse(context: $sharedContext);

        $this->assertCount(
            expectedCount: 1,
            haystack: $html
        );
        $this->assertEquals(
            expected: $htmlContent,
            actual: $html[0]->getContent()
        );
        $this->assertCount(
            expectedCount:2,
            haystack: $html[0]->getChildren()
        );
        $this->assertCount(
            expectedCount:1,
            haystack: array_filter(
                array: $html[0]->getChildren(),
                callback: fn (AbstractParser $parser): bool => $parser instanceof HeadParser
            )
        );
        $this->assertCount(
            expectedCount:1,
            haystack: array_filter(
                array: $html[0]->getChildren(),
                callback: fn (AbstractParser $parser): bool => $parser instanceof BodyParser
            )
        );
    }
}

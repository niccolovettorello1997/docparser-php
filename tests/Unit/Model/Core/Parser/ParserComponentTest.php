<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Core\Parser;

use Niccolo\DocparserPhp\Model\Core\Parser\ParserComponent;
use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\HtmlParser;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\DoctypeParser;

class ParserComponentTest extends TestCase
{
    public function test_parse(): void
    {
        $doctypeContent = <<<HTML

        <html lang="en">
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
        </html>

        HTML;

        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/valid_html.html");
        $parserComponent = ParserComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Parser/parser_html.yaml"
        );

        $result = $parserComponent->run();

        $this->assertCount(
            expectedCount: 1,
            haystack: $result,
        );
        $this->assertInstanceOf(
            expected: DoctypeParser::class,
            actual: $result[0],
        );
        $this->assertEquals(
            expected: $doctypeContent,
            actual: $result[0]->getContent()
        );
        $this->assertCount(
            expectedCount:1,
            haystack: $result[0]->getChildren()
        );
        $this->assertInstanceOf(
            expected: HtmlParser::class,
            actual: $result[0]->getChildren()[0]
        );
    }
}

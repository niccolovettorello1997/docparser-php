<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Unit\Model\Parser\HTML\Parser;

use DocparserPhp\Model\Core\Parser\Node;
use DocparserPhp\Model\Parser\HTML\Element\BodyParser;
use DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;
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

        $this->assertNotNull($bodyNode);
        $this->assertEquals(
            HtmlElementType::BODY->value,
            $bodyNode->getTagName()
        );
        $this->assertNull($bodyNode->getContent());
        $this->assertCount(
            2,
            $bodyNode->getChildren()
        );
        $this->assertCount(
            1,
            array_filter(
                array: $bodyNode->getChildren(),
                callback: fn (Node $node): bool => $node->getTagName() === HtmlElementType::HEADINGS->value
            )
        );
        $this->assertCount(
            1,
            array_filter(
                array: $bodyNode->getChildren(),
                callback: fn (Node $node): bool => $node->getTagName() === HtmlElementType::PARAGRAPHS->value
            )
        );
    }

    public function test_parse_body_with_attributes(): void
    {
        $content = <<<HTML

        <head>
          <title>Example Document</title>
        </head>
        <body text="green">
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

        $expectedAttributes = ['text' => 'green'];

        $bodyParser = new BodyParser();
        $bodyNode = $bodyParser->parse(content: $content);

        $this->assertNotNull($bodyNode);
        $this->assertEquals(
            HtmlElementType::BODY->value,
            $bodyNode->getTagName()
        );
        $this->assertNull($bodyNode->getContent());
        $this->assertCount(
            2,
            $bodyNode->getChildren()
        );
        $this->assertCount(
            1,
            array_filter(
                array: $bodyNode->getChildren(),
                callback: fn (Node $node): bool => $node->getTagName() === HtmlElementType::HEADINGS->value
            )
        );
        $this->assertCount(
            1,
            array_filter(
                array: $bodyNode->getChildren(),
                callback: fn (Node $node): bool => $node->getTagName() === HtmlElementType::PARAGRAPHS->value
            )
        );
        $this->assertEquals($expectedAttributes, $bodyNode->getAttributes());
    }
}

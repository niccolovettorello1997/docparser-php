<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Parser\HTML\Parser;

use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\HeadingParser;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;
use PHPUnit\Framework\TestCase;

class HeadingParserTest extends TestCase
{
    public function test_parse_headings(): void
    {
        $firstHeadingContent = 'Welcome to My Page';
        $secondHeadingContent = 'Section 1';
        $thirdHeadingContent = 'Section 2';

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

        $headingParser = new HeadingParser();
        $headingNode = $headingParser->parse(content: $body);

        $this->assertNotNull($headingNode);
        $this->assertEquals(
            HtmlElementType::HEADINGS->value,
            $headingNode->getTagName()
        );
        $this->assertCount(
            3,
            $headingNode->getChildren()
        );
        $this->assertCount(
            1,
            array_filter(
                array: $headingNode->getChildren(),
                callback: fn (Node $node): bool => $node->getContent() === $firstHeadingContent
            )
        );
        $this->assertCount(
            1,
            array_filter(
                array: $headingNode->getChildren(),
                callback: fn (Node $node): bool => $node->getContent() === $secondHeadingContent
            )
        );
        $this->assertCount(
            1,
            array_filter(
                array: $headingNode->getChildren(),
                callback: fn (Node $node): bool => $node->getContent() === $thirdHeadingContent
            )
        );
    }

    public function test_parse_headings_with_attributes(): void
    {
        $firstHeadingContent = 'Welcome to My Page';
        $secondHeadingContent = 'Section 1';
        $thirdHeadingContent = 'Section 2';

        $body = <<<BODY

        <header>
            <h1 title="My title">Welcome to My Page</h1>
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

        $expectedAttributes = ['title' => 'My title'];

        $headingParser = new HeadingParser();
        $headingNode = $headingParser->parse(content: $body);

        $this->assertNotNull($headingNode);
        $this->assertEquals(
            HtmlElementType::HEADINGS->value,
            $headingNode->getTagName()
        );
        $this->assertCount(
            3,
            $headingNode->getChildren()
        );
        $this->assertCount(
            1,
            array_filter(
                array: $headingNode->getChildren(),
                callback: fn (Node $node): bool => $node->getContent() === $firstHeadingContent
            )
        );
        $this->assertCount(
            1,
            array_filter(
                array: $headingNode->getChildren(),
                callback: fn (Node $node): bool => $node->getContent() === $secondHeadingContent
            )
        );
        $this->assertCount(
            1,
            array_filter(
                array: $headingNode->getChildren(),
                callback: fn (Node $node): bool => $node->getContent() === $thirdHeadingContent
            )
        );
        $this->assertEquals(
            $expectedAttributes,
            array_values(
                array_filter(
                    array: $headingNode->getChildren(),
                    callback: fn (Node $node): bool => $node->getContent() === $firstHeadingContent
                )
            )[0]->getAttributes()
        );
    }
}

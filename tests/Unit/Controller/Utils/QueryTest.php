<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Controller\Utils;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Controller\Utils\Query;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\InputType;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\RenderingType;

class QueryTest extends TestCase
{
    public function test_build_context_with_text_area(): void
    {
        $files = [];

        $data = [];
        $data['type'] = InputType::HTML->value;
        $data['renderingType'] = RenderingType::HTML->value;
        $data['context'] = <<<HTML

        <!DOCTYPE html>
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

        $query = Query::getQuery(
            data: $data,
            files: $files,
        );

        $this->assertEquals(
            expected: $data['context'],
            actual: $query->getContext(),
        );
        $this->assertEquals(
            expected: InputType::HTML->value,
            actual: $query->getInputType()->value,
        );
        $this->assertEquals(
            expected: RenderingType::HTML->value,
            actual: $query->getRenderingType()->value,
        );
    }

    public function test_build_context_with_valid_file(): void
    {
        $path = __DIR__ . "/../../../../fixtures/tests/valid_html.html";
        $html = file_get_contents(filename: $path);

        $files = [];
        $files['file']['name'] = 'valid_html.html';
        $files['file']['tmp_name'] = $path;

        $data = [];
        $data['context'] = '';
        $data['type'] = InputType::HTML->value;
        $data['renderingType'] = RenderingType::HTML->value;

        $query = Query::getQuery(
            data: $data,
            files: $files,
        );

        $this->assertEquals(
            expected: $html,
            actual: $query->getContext(),
        );
        $this->assertEquals(
            expected: InputType::HTML->value,
            actual: $query->getInputType()->value,
        );
        $this->assertEquals(
            expected: RenderingType::HTML->value,
            actual: $query->getRenderingType()->value,
        );
    }

    public function test_build_context_with_invalid_file(): void
    {
        $files = [];
        $files['file']['name'] = 'invalid_html_format.c';
        $files['file']['tmp_name'] = __DIR__ . "/../../../../fixtures/tests/invalid_html_format.c";

        $data = [];
        $data['context'] = '';
        $data['type'] = InputType::HTML->value;
        $data['renderingType'] = RenderingType::HTML->value;

        $query = Query::getQuery(
            data: $data,
            files: $files,
        );

        $this->assertNull(actual: $query);
    }
}

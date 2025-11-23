<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Integration;

use DocparserPhp\Model\Utils\Parser\Enum\InputType;
use DocparserPhp\Service\ParserService;
use DocparserPhp\Service\Utils\Query;
use DocparserPhp\Service\ValidatorService;
use DocparserPhp\View\HtmlParserView;
use PHPUnit\Framework\TestCase;

class UIIntegrationTest extends TestCase
{
    public function test_valid_html(): void
    {
        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <title>Example</title>
            </head>
            <body>
                <p>Hello World</p>
            </body>
        </html>
        HTML;

        $query = new Query(
            context: $html,
            inputType: InputType::HTML
        );

        $validatorService = new ValidatorService();
        $parserService = new ParserService();

        $htmlParserView = new HtmlParserView(
            elementValidationResult: $validatorService->runValidation(query: $query),
            tree: $parserService->parse(query: $query)
        );

        $rendered = $htmlParserView->render();

        $this->assertStringContainsString(
            '<li>Valid: yes</li>',
            $rendered,
        );
        $this->assertStringContainsString(
            '<li>0: <ul><li>Name: title</li><li>Content: Example</li></ul></li>',
            $rendered,
        );
        $this->assertStringContainsString(
            '<li>0: <ul><li>Name: p</li><li>Content: Hello World</li></ul></li>',
            $rendered,
        );
        $this->assertStringNotContainsString(
            '<div><strong>Errors: </strong>',
            $rendered,
        );
        $this->assertStringNotContainsString(
            '<div><strong>Warnings: </strong>',
            $rendered,
        );
    }

    public function test_invalid_html_missing_head_and_title(): void
    {
        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
            <body>
                <p>Hello World</p>
            </body>
        </html>
        HTML;

        $query = new Query(
            context: $html,
            inputType: InputType::HTML
        );

        $validatorService = new ValidatorService();

        $htmlParserView = new HtmlParserView(
            elementValidationResult: $validatorService->runValidation(query: $query)
        );

        $rendered = $htmlParserView->render();

        $this->assertStringContainsString(
            '<li>Errors: ',
            $rendered,
        );
        $this->assertStringContainsString(
            '<li>message: head element in the html element is missing or incorrectly written.</li>',
            $rendered,
        );
        $this->assertStringContainsString(
            '<li>message: The title element is missing or not written properly.</li>',
            $rendered,
        );
        $this->assertStringNotContainsString(
            '<li>Valid: yes</li>',
            $rendered,
        );
    }

    public function test_invalid_html_nested_paragraph_and_heading(): void
    {
        $nestedHeadingMessage = htmlspecialchars(string: 'Invalid content inside heading element <h1> : contains <h2> tag.');

        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <title>Example</title>
            </head>
            <body>
                <h1>Welcome <h2>guest</h2></h1>
                <p>Hello World <p>beautiful day</p></p>
            </body>
        </html>
        HTML;

        $query = new Query(
            context: $html,
            inputType: InputType::HTML
        );

        $validatorService = new ValidatorService();

        $htmlParserView = new HtmlParserView(
            elementValidationResult: $validatorService->runValidation(query: $query)
        );

        $rendered = $htmlParserView->render();

        $this->assertStringContainsString(
            '<li>Errors: ',
            $rendered,
        );
        $this->assertStringContainsString(
            '<li>message: Nested paragraph elements are not allowed in paragraph element.</li>',
            $rendered,
        );
        $this->assertStringContainsString(
            "<li>message: {$nestedHeadingMessage}</li>",
            $rendered,
        );
        $this->assertStringNotContainsString(
            '<div>Your content is valid!</div>',
            $rendered,
        );
    }

    public function test_invalid_html_invalid_body_content(): void
    {
        $invalidBodyContentMessage = htmlspecialchars(string: 'Invalid tag <meta> detected in body element.');
        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
            <head>
                <title>Example</title>
            </head>
            <body>
                <meta charset="UTF-8">
                <p>Hello World</p>
            </body>
        </html>
        HTML;

        $query = new Query(
            context: $html,
            inputType: InputType::HTML
        );

        $validatorService = new ValidatorService();

        $htmlParserView = new HtmlParserView(
            elementValidationResult: $validatorService->runValidation(query: $query)
        );

        $rendered = $htmlParserView->render();

        $this->assertStringContainsString(
            '<li>Errors: ',
            $rendered,
        );
        $this->assertStringContainsString(
            "<li>message: {$invalidBodyContentMessage}</li>",
            $rendered,
        );
        $this->assertStringNotContainsString(
            '<li>Valid: yes</li>',
            $rendered,
        );
    }

    public function test_valid_html_with_warnings(): void
    {
        $html = <<<HTML
        <!DOCTYPE html>
        <html>
            <head>
                <title>Example</title>
            </head>
            <body>
                <p></p>
            </body>
        </html>
        HTML;

        $query = new Query(
            context: $html,
            inputType: InputType::HTML
        );

        $validatorService = new ValidatorService();
        $parserService = new ParserService();

        $htmlParserView = new HtmlParserView(
            elementValidationResult: $validatorService->runValidation(query: $query),
            tree: $parserService->parse(query: $query)
        );

        $rendered = $htmlParserView->render();

        $this->assertStringContainsString(
            '<li>Errors: <ul></ul>',
            $rendered,
        );
        $this->assertStringContainsString(
            '<li>Valid: yes</li>',
            $rendered,
        );
        $this->assertStringContainsString(
            '<li>Warnings: ',
            $rendered,
        );
        $this->assertStringContainsString(
            '<li>message: Empty paragraph element detected.</li>',
            $rendered,
        );
        $this->assertStringContainsString(
            '<li>message: html element should have a lang attribute.</li>',
            $rendered,
        );
        $this->assertStringContainsString(
            '<li>0: <ul><li>Name: title</li><li>Content: Example</li></ul></li>',
            $rendered,
        );
        $this->assertStringContainsString(
            '<li>0: <ul><li>Name: p</li><li>Content: </li></ul></li>',
            $rendered,
        );
    }

    public function test_invalid_html_with_warnings_and_errors(): void
    {
        $html = <<<HTML
        <!DOCTYPE html>
        <html>
            <head>
                <title>Example</title>
                <title>Duplicate Title</title>
            </head>
            <body>
                <p></p>
                <h1>Header without closing tag
            </body>
        </html>
        HTML;

        $query = new Query(
            context: $html,
            inputType: InputType::HTML
        );

        $validatorService = new ValidatorService();

        $htmlParserView = new HtmlParserView(
            elementValidationResult: $validatorService->runValidation(query: $query),
        );

        $rendered = $htmlParserView->render();

        $this->assertStringNotContainsString(
            '<li>Valid: yes</li>',
            $rendered,
        );
        $this->assertStringContainsString(
            '<li>Errors: <ul>',
            $rendered,
        );
        $this->assertStringNotContainsString(
            '<li>Errors: <ul><\ul>',
            $rendered,
        );
        $this->assertStringContainsString(
            '<li>Warnings: <ul>',
            $rendered,
        );
        $this->assertStringNotContainsString(
            '<li>Warnings: <ul><\ul>',
            $rendered,
        );
        $this->assertStringContainsString(
            '<li>message: Empty paragraph element detected.</li>',
            $rendered,
        );
        $this->assertStringContainsString(
            '<li>message: html element should have a lang attribute.</li>',
            $rendered,
        );
        $this->assertStringContainsString(
            '<li>message: The title element must be unique in the HTML document.</li>',
            $rendered,
        );
        $this->assertStringContainsString(
            '<li>message: Unclosed heading element(s) detected.</li>',
            $rendered,
        );
    }

    public function test_stub_markdown(): void
    {
        $markdown = '# Example Title';

        $query = new Query(
            context: $markdown,
            inputType: InputType::MARKDOWN
        );

        $validatorService = new ValidatorService();
        $parserService = new ParserService();

        $htmlParserView = new HtmlParserView(
            elementValidationResult: $validatorService->runValidation(query: $query),
            tree: $parserService->parse(query: $query)
        );

        $rendered = $htmlParserView->render();

        $this->assertStringContainsString(
            '<li>Valid: yes</li>',
            $rendered,
        );
        $this->assertStringContainsString(
            '<li>Errors: <ul></ul>',
            $rendered,
        );
        $this->assertStringContainsString(
            '<li>Warnings: <ul></ul>',
            $rendered,
        );
        $this->assertStringContainsString(
            '<li>0: <ul><li>Name: markdown</li><li>Content: # Example Title</li></ul></li>',
            $rendered,
        );
    }
}

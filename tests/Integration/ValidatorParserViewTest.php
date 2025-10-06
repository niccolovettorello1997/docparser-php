<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Integration;

use Niccolo\DocparserPhp\Model\Core\Parser\ParserComponentFactory;
use Niccolo\DocparserPhp\Model\Core\Validator\ValidatorComponentFactory;
use Niccolo\DocparserPhp\View\ElementValidationResultView;
use Niccolo\DocparserPhp\View\Parser\HtmlParserView;
use PHPUnit\Framework\TestCase;

class ValidatorParserViewTest extends TestCase
{
    public function test_valid_html(): void
    {
        $type = 'html';
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

        $validatorComponent = ValidatorComponentFactory::getValidatorComponent(
            context: $html,
            inputType: $type,
        );

        $parserComponent = ParserComponentFactory::getParserComponent(
            context: $html,
            inputType: $type,
        );

        $validationResultView = new ElementValidationResultView(
            elementValidationResult: $validatorComponent->run(),
        );

        $parsingResultView = new HtmlParserView(tree: $parserComponent->run());

        $validationRender = $validationResultView->render();
        $parsingRender = $parsingResultView->render();

        $this->assertStringContainsString(
            '<div>Your content is valid!</div>',
            $validationRender,
        );
        $this->assertStringContainsString(
            '<li>0: <ul><li>Name: title</li><li>Content: Example</li></ul></li>',
            $parsingRender,
        );
        $this->assertStringContainsString(
            '<li>0: <ul><li>Name: p</li><li>Content: Hello World</li></ul></li>',
            $parsingRender,
        );
        $this->assertStringNotContainsString(
            '<div><strong>Errors: </strong>',
            $validationRender,
        );
        $this->assertStringNotContainsString(
            '<div><strong>Warnings: </strong>',
            $validationRender,
        );
    }

    public function test_invalid_html_missing_head_and_title(): void
    {
        $type = 'html';
        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
            <body>
                <p>Hello World</p>
            </body>
        </html>
        HTML;

        $validatorComponent = ValidatorComponentFactory::getValidatorComponent(
            context: $html,
            inputType: $type,
        );

        $validationResultView = new ElementValidationResultView(
            elementValidationResult: $validatorComponent->run(),
        );

        $validationRender = $validationResultView->render();

        $this->assertStringContainsString(
            '<strong>Errors: </strong>',
            $validationRender,
        );
        $this->assertStringContainsString(
            '<li>head element in the html element is missing or incorrectly written.</li>',
            $validationRender,
        );
        $this->assertStringContainsString(
            '<li>The title element is missing or not written properly.</li>',
            $validationRender,
        );
        $this->assertStringNotContainsString(
            '<div>Your content is valid!</div>',
            $validationRender,
        );
    }

    public function test_invalid_html_nested_paragraph_and_heading(): void
    {
        $nestedHeadingMessage = htmlspecialchars(string: 'Invalid content inside heading element <h1> : contains <h2> tag.');
        $type = 'html';
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

        $validatorComponent = ValidatorComponentFactory::getValidatorComponent(
            context: $html,
            inputType: $type,
        );

        $validationResultView = new ElementValidationResultView(
            elementValidationResult: $validatorComponent->run(),
        );

        $validationRender = $validationResultView->render();

        $this->assertStringContainsString(
            '<strong>Errors: </strong>',
            $validationRender,
        );
        $this->assertStringContainsString(
            '<li>Nested paragraph elements are not allowed in paragraph element.</li>',
            $validationRender,
        );
        $this->assertStringContainsString(
            "<li>{$nestedHeadingMessage}</li>",
            $validationRender,
        );
        $this->assertStringNotContainsString(
            '<div>Your content is valid!</div>',
            $validationRender,
        );
    }

    public function test_invalid_html_invalid_body_content(): void
    {
        $invalidBodyContentMessage = htmlspecialchars(string: 'Invalid tag <meta> detected in body element.');
        $type = 'html';
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

        $validatorComponent = ValidatorComponentFactory::getValidatorComponent(
            context: $html,
            inputType: $type,
        );

        $validationResultView = new ElementValidationResultView(
            elementValidationResult: $validatorComponent->run(),
        );

        $validationRender = $validationResultView->render();

        $this->assertStringContainsString(
            '<strong>Errors: </strong>',
            $validationRender,
        );
        $this->assertStringContainsString(
            "<li>{$invalidBodyContentMessage}</li>",
            $validationRender,
        );
        $this->assertStringNotContainsString(
            '<div>Your content is valid!</div>',
            $validationRender,
        );
    }

    public function test_valid_html_with_warnings(): void
    {
        $type = 'html';
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

        $validatorComponent = ValidatorComponentFactory::getValidatorComponent(
            context: $html,
            inputType: $type,
        );

        $parserComponent = ParserComponentFactory::getParserComponent(
            context: $html,
            inputType: $type,
        );

        $validationResultView = new ElementValidationResultView(
            elementValidationResult: $validatorComponent->run(),
        );

        $parsingResultView = new HtmlParserView(tree: $parserComponent->run());

        $validationRender = $validationResultView->render();
        $parsingRender = $parsingResultView->render();

        $this->assertStringNotContainsString(
            '<strong>Errors: </strong>',
            $validationRender,
        );
        $this->assertStringContainsString(
            '<div>Your content is valid!</div>',
            $validationRender,
        );
        $this->assertStringContainsString(
            '<strong>Warnings: </strong>',
            $validationRender,
        );
        $this->assertStringContainsString(
            '<li>Empty paragraph element detected.</li>',
            $validationRender,
        );
        $this->assertStringContainsString(
            '<li>html element should have a lang attribute.</li>',
            $validationRender,
        );
        $this->assertStringContainsString(
            '<li>0: <ul><li>Name: title</li><li>Content: Example</li></ul></li>',
            $parsingRender,
        );
        $this->assertStringContainsString(
            '<li>0: <ul><li>Name: p</li><li>Content: </li></ul></li>',
            $parsingRender,
        );
    }

    public function test_invalid_html_with_warnings_and_errors(): void
    {
        $type = 'html';
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

        $validatorComponent = ValidatorComponentFactory::getValidatorComponent(
            context: $html,
            inputType: $type,
        );

        $validationResultView = new ElementValidationResultView(
            elementValidationResult: $validatorComponent->run(),
        );

        $validationRender = $validationResultView->render();

        $this->assertStringNotContainsString(
            '<div>Your content is valid!</div>',
            $validationRender,
        );
        $this->assertStringContainsString(
            '<strong>Errors: </strong>',
            $validationRender,
        );
        $this->assertStringContainsString(
            '<strong>Warnings: </strong>',
            $validationRender,
        );
        $this->assertStringContainsString(
            '<li>Empty paragraph element detected.</li>',
            $validationRender,
        );
        $this->assertStringContainsString(
            '<li>html element should have a lang attribute.</li>',
            $validationRender,
        );
        $this->assertStringContainsString(
            '<li>The title element must be unique in the HTML document.</li>',
            $validationRender,
        );
        $this->assertStringContainsString(
            '<li>Unclosed heading element(s) detected.</li>',
            $validationRender,
        );
    }

    public function test_stub_markdown(): void
    {
        $type = 'markdown';
        $markdown = '# Example Title';

        $validatorComponent = ValidatorComponentFactory::getValidatorComponent(
            context: $markdown,
            inputType: $type,
        );

        $parserComponent = ParserComponentFactory::getParserComponent(
            context: $markdown,
            inputType: $type,
        );

        $validationResultView = new ElementValidationResultView(
            elementValidationResult: $validatorComponent->run(),
        );

        $parsingResultView = new HtmlParserView(tree: $parserComponent->run());

        $validationRender = $validationResultView->render();
        $parsingRender = $parsingResultView->render();

        $this->assertStringContainsString(
            '<div>Your content is valid!</div>',
            $validationRender,
        );
        $this->assertStringNotContainsString(
            '<div><strong>Errors: </strong>',
            $validationRender,
        );
        $this->assertStringNotContainsString(
            '<div><strong>Warnings: </strong>',
            $validationRender,
        );
        $this->assertStringContainsString(
            '<li>0: <ul><li>Name: markdown</li><li>Content: # Example Title</li></ul></li>',
            $parsingRender,
        );
    }
}

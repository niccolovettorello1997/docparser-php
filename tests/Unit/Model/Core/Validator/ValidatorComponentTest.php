<?php

declare(strict_types= 1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Core\Validator;

use Niccolo\DocparserPhp\Model\Core\Validator\ValidatorComponent;
use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\Model\Utils\Error\MissingElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\NotUniqueElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\StructuralError;
use Niccolo\DocparserPhp\Model\Utils\Warning\EmptyElementWarning;
use Niccolo\DocparserPhp\Model\Utils\Warning\RecommendedAttributeWarning;
use PHPUnit\Framework\TestCase;

class ValidatorComponentTest extends TestCase
{
    private function assertMessageExists(array $items, string $expectedMessage): void
    {
        $found = array_filter(
            array: $items,
            callback: fn($e): bool => $e->getMessage() === $expectedMessage
        );

        $this->assertNotEmpty(actual: $found);
    }

    public function test_html_validator_component_valid_html(): void
    {
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/valid_html.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertTrue(condition: $result->isValid());
        $this->assertEmpty(actual: $result->getErrors());
        $this->assertEmpty(actual: $result->getWarnings());
    }

    public function test_html_validator_component_invalid_html_body(): void
    {
        $expectedErrorMessage = 'Invalid tag <meta> detected in body element.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/invalid_html_body.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotEmpty(actual: $result->getErrors());
        $this->assertInstanceOf(
            expected: InvalidContentError::class,
            actual: $result->getErrors()[0],
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getErrors()[0]->getMessage(),
        );
    }

    public function test_html_validator_component_invalid_html_doctype(): void
    {
        $expectedErrorMessage = 'The doctype element is missing.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/invalid_html_doctype.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotEmpty(actual: $result->getErrors());
        $this->assertInstanceOf(
            expected: MissingElementError::class,
            actual: $result->getErrors()[0],
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getErrors()[0]->getMessage(),
        );
    }

    public function test_html_validator_component_invalid_html_heading(): void
    {
        $expectedErrorMessage = 'Empty content inside heading element <h1>.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/invalid_html_heading.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotEmpty(actual: $result->getErrors());
        $this->assertInstanceOf(
            expected: InvalidContentError::class,
            actual: $result->getErrors()[0],
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getErrors()[0]->getMessage(),
        );
    }

    public function test_html_validator_component_invalid_html_head(): void
    {
        $expectedErrorMessage = 'Nested body element detected in head element.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/invalid_html_head.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotEmpty(actual: $result->getErrors());
        $this->assertInstanceOf(
            expected: StructuralError::class,
            actual: $result->getErrors()[0],
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getErrors()[0]->getMessage(),
        );
    }

    public function test_html_validator_component_invalid_html_html(): void
    {
        $expectedErrorMessage = 'The required element html is missing or incorrectly written.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/invalid_html_html.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotEmpty(actual: $result->getErrors());
        $this->assertInstanceOf(
            expected: MissingElementError::class,
            actual: $result->getErrors()[0],
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getErrors()[0]->getMessage(),
        );
    }

    public function test_html_validator_component_invalid_html_paragraph(): void
    {
        $expectedErrorMessage = 'Nested paragraph elements are not allowed in paragraph element.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/invalid_html_paragraph.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotEmpty(actual: $result->getErrors());
        $this->assertInstanceOf(
            expected: StructuralError::class,
            actual: $result->getErrors()[0],
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getErrors()[0]->getMessage(),
        );
    }

    public function test_html_validator_component_invalid_html_title(): void
    {
        $expectedErrorMessage = 'The title element must be unique in the HTML document.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/invalid_html_title.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotEmpty(actual: $result->getErrors());
        $this->assertInstanceOf(
            expected: NotUniqueElementError::class,
            actual: $result->getErrors()[0],
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getErrors()[0]->getMessage(),
        );
    }

    public function test_html_validator_component_valid_html_empty_element(): void
    {
        $expectedWarningMessage = 'Empty paragraph element detected.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/valid_html_empty_element.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertTrue(condition: $result->isValid());
        $this->assertEmpty(actual: $result->getErrors());
        $this->assertNotEmpty(actual: $result->getWarnings());
        $this->assertCount(expectedCount: 1, haystack: $result->getWarnings());
        $this->assertInstanceOf(
            expected: EmptyElementWarning::class,
            actual: $result->getWarnings()[0],
        );
        $this->assertEquals(
            expected: $expectedWarningMessage,
            actual: $result->getWarnings()[0]->getMessage(),
        );
    }

    public function test_html_validator_component_valid_html_recommended_attribute(): void
    {
        $expectedWarningMessage = 'html element should have a lang attribute.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/valid_html_recommended_attribute.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertTrue(condition: $result->isValid());
        $this->assertEmpty(actual: $result->getErrors());
        $this->assertNotEmpty(actual: $result->getWarnings());
        $this->assertCount(expectedCount: 1, haystack: $result->getWarnings());
        $this->assertInstanceOf(
            expected: RecommendedAttributeWarning::class,
            actual: $result->getWarnings()[0],
        );
        $this->assertEquals(
            expected: $expectedWarningMessage,
            actual: $result->getWarnings()[0]->getMessage(),
        );
    }

    public function test_html_validator_component_missing_title_and_head(): void
    {
        $missingHeadElementMessage = 'head element in the html element is missing or incorrectly written.';
        $missingTitleElementMessage = 'The title element is missing or not written properly.';

        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="de">
            <body>
                <p>Example paragraph</p>
            </body>
        </html>
        HTML;

        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotEmpty(actual: $result->getErrors());
        $this->assertMessageExists(items: $result->getErrors(), expectedMessage: $missingHeadElementMessage);
        $this->assertMessageExists(items: $result->getErrors(), expectedMessage: $missingTitleElementMessage);
    }

    public function test_html_validator_component_duplicate_title_and_nested_heading(): void
    {
        $duplicateTitleElementMessage = 'The title element must be unique in the HTML document.';
        $invalidHeadingElementContentMessage = 'Invalid content inside heading element <h1> : contains <h2> tag.';

        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="de">
            <head>
                <title>Sample Title</title>
                <title>Duplicate Title</title>
            </head>
            <body>
                <h1><h2>Nested Heading</h2></h1>
                <p>Example paragraph</p>
            </body>
        </html>
        HTML;

        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotEmpty(actual: $result->getErrors());
        $this->assertMessageExists(items: $result->getErrors(), expectedMessage: $duplicateTitleElementMessage);
        $this->assertMessageExists(items: $result->getErrors(), expectedMessage: $invalidHeadingElementContentMessage);
    }

    public function test_html_validator_component_empty_title_and_paragraph(): void
    {
        $duplicateTitleElementMessage = 'The title element must not be empty.';
        $emptyParagraphElementMessage = 'Empty paragraph element detected.';

        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="de">
            <head>
                <title></title>
            </head>
            <body>
                <p></p>
            </body>
        </html>
        HTML;

        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotEmpty(actual: $result->getErrors());
        $this->assertNotEmpty(actual: $result->getWarnings());
        $this->assertMessageExists(items: $result->getErrors(), expectedMessage: $duplicateTitleElementMessage);
        $this->assertMessageExists(items: $result->getWarnings(), expectedMessage: $emptyParagraphElementMessage);
    }

    public function test_html_validator_component_invalid_content_body_and_head(): void
    {
        $invalidContentHeadElementMessage = 'Nested html element detected in head element.';
        $invalidContentBodyElementMessage = 'Invalid tag <meta> detected in body element.';

        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="de">
            <head>
                <title>Example title</title>
                <html></html>
            </head>
            <body>
                <meta charset="UTF-8">
                <p>Example paragraph</p>
            </body>
        </html>
        HTML;

        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotEmpty(actual: $result->getErrors());
        $this->assertMessageExists(items: $result->getErrors(), expectedMessage: $invalidContentHeadElementMessage);
        $this->assertMessageExists(items: $result->getErrors(), expectedMessage: $invalidContentBodyElementMessage);
    }

    public function test_html_validator_component_invalid_content_heading_and_paragraph(): void
    {
        $invalidContentHeadingElementMessage = 'Invalid content inside heading element <h1> : contains <meta> tag.';
        $invalidContentParagraphElementMessage = 'Invalid tag <div> found within paragraph element.';

        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="de">
            <head>
                <title>Example title</title>
            </head>
            <body>
                <h1><meta charset="UTF-8">Example title</h1>
                <p><div>Example paragraph</div></p>
            </body>
        </html>
        HTML;

        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotEmpty(actual: $result->getErrors());
        $this->assertMessageExists(items: $result->getErrors(), expectedMessage: $invalidContentHeadingElementMessage);
        $this->assertMessageExists(items: $result->getErrors(), expectedMessage: $invalidContentParagraphElementMessage);
    }

    public function test_html_validator_component_empty_paragraph_and_without_lang_attribute(): void
    {
        $recommendedLangAttributeMessage = 'html element should have a lang attribute.';
        $emptyParagraphElementMessage = 'Empty paragraph element detected.';

        $html = <<<HTML
        <!DOCTYPE html>
        <html>
            <head>
                <title>Example title</title>
            </head>
            <body>
                <h1>Example title</h1>
                <p></p>
            </body>
        </html>
        HTML;

        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertTrue(condition: $result->isValid());
        $this->assertEmpty(actual: $result->getErrors());
        $this->assertNotEmpty(actual: $result->getWarnings());
        $this->assertMessageExists(items: $result->getWarnings(), expectedMessage: $recommendedLangAttributeMessage);
        $this->assertMessageExists(items: $result->getWarnings(), expectedMessage: $emptyParagraphElementMessage);
    }
}

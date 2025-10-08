<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Core\Validator;

use Niccolo\DocparserPhp\Model\Core\Validator\ValidatorComponent;
use Niccolo\DocparserPhp\Model\Utils\Error\AbstractError;
use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\Model\Utils\Error\MissingElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\NotUniqueElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\StructuralError;
use Niccolo\DocparserPhp\Model\Utils\Warning\AbstractWarning;
use Niccolo\DocparserPhp\Model\Utils\Warning\EmptyElementWarning;
use Niccolo\DocparserPhp\Model\Utils\Warning\RecommendedAttributeWarning;
use PHPUnit\Framework\TestCase;

class ValidatorComponentTest extends TestCase
{
    /**
     * Assert that the array of items contains the expected message.
     *
     * @param array<AbstractError|AbstractWarning> $items
     * @param string                               $expectedMessage
     */
    private function assertMessageExists(array $items, string $expectedMessage): void
    {
        $found = array_filter(
            array: $items,
            callback: fn ($e): bool => $e->getMessage() === $expectedMessage
        );

        $this->assertNotEmpty($found);
    }

    public function test_html_validator_component_valid_html(): void
    {
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/valid_html.html");

        if (false === $html) {
            $this->fail('Failed to read the HTML fixture file.');
        }

        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->getErrors());
        $this->assertEmpty($result->getWarnings());
    }

    public function test_html_validator_component_invalid_html_body(): void
    {
        $expectedErrorMessage = 'Invalid tag <meta> detected in body element.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/invalid_html_body.html");

        if (false === $html) {
            $this->fail('Failed to read the HTML fixture file.');
        }

        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse($result->isValid());
        $this->assertNotEmpty($result->getErrors());
        $this->assertInstanceOf(
            InvalidContentError::class,
            $result->getErrors()[0],
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $result->getErrors()[0]->getMessage(),
        );
    }

    public function test_html_validator_component_invalid_html_doctype(): void
    {
        $expectedErrorMessage = 'The doctype element is missing.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/invalid_html_doctype.html");

        if (false === $html) {
            $this->fail('Failed to read the HTML fixture file.');
        }

        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse($result->isValid());
        $this->assertNotEmpty($result->getErrors());
        $this->assertInstanceOf(
            MissingElementError::class,
            $result->getErrors()[0],
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $result->getErrors()[0]->getMessage(),
        );
    }

    public function test_html_validator_component_invalid_html_heading(): void
    {
        $expectedErrorMessage = 'Empty content inside heading element <h1>.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/invalid_html_heading.html");

        if (false === $html) {
            $this->fail('Failed to read the HTML fixture file.');
        }

        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse($result->isValid());
        $this->assertNotEmpty($result->getErrors());
        $this->assertInstanceOf(
            InvalidContentError::class,
            $result->getErrors()[0],
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $result->getErrors()[0]->getMessage(),
        );
    }

    public function test_html_validator_component_invalid_html_head(): void
    {
        $expectedErrorMessage = 'Nested body element detected in head element.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/invalid_html_head.html");

        if (false === $html) {
            $this->fail('Failed to read the HTML fixture file.');
        }

        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse($result->isValid());
        $this->assertNotEmpty($result->getErrors());
        $this->assertInstanceOf(
            StructuralError::class,
            $result->getErrors()[0],
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $result->getErrors()[0]->getMessage(),
        );
    }

    public function test_html_validator_component_invalid_html_html(): void
    {
        $expectedErrorMessage = 'The required element html is missing or incorrectly written.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/invalid_html_html.html");

        if (false === $html) {
            $this->fail('Failed to read the HTML fixture file.');
        }

        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse($result->isValid());
        $this->assertNotEmpty($result->getErrors());
        $this->assertInstanceOf(
            MissingElementError::class,
            $result->getErrors()[0],
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $result->getErrors()[0]->getMessage(),
        );
    }

    public function test_html_validator_component_invalid_html_paragraph(): void
    {
        $expectedErrorMessage = 'Nested paragraph elements are not allowed in paragraph element.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/invalid_html_paragraph.html");

        if (false === $html) {
            $this->fail('Failed to read the HTML fixture file.');
        }

        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse($result->isValid());
        $this->assertNotEmpty($result->getErrors());
        $this->assertInstanceOf(
            StructuralError::class,
            $result->getErrors()[0],
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $result->getErrors()[0]->getMessage(),
        );
    }

    public function test_html_validator_component_invalid_html_title(): void
    {
        $expectedErrorMessage = 'The title element must be unique in the HTML document.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/invalid_html_title.html");

        if (false === $html) {
            $this->fail('Failed to read the HTML fixture file.');
        }

        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse($result->isValid());
        $this->assertNotEmpty($result->getErrors());
        $this->assertInstanceOf(
            NotUniqueElementError::class,
            $result->getErrors()[0],
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $result->getErrors()[0]->getMessage(),
        );
    }

    public function test_html_validator_component_valid_html_empty_element(): void
    {
        $expectedWarningMessage = 'Empty paragraph element detected.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/valid_html_empty_element.html");

        if (false === $html) {
            $this->fail('Failed to read the HTML fixture file.');
        }

        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->getErrors());
        $this->assertNotEmpty($result->getWarnings());
        $this->assertCount(
            1,
            $result->getWarnings()
        );
        $this->assertInstanceOf(
            EmptyElementWarning::class,
            $result->getWarnings()[0],
        );
        $this->assertEquals(
            $expectedWarningMessage,
            $result->getWarnings()[0]->getMessage(),
        );
    }

    public function test_html_validator_component_valid_html_recommended_attribute(): void
    {
        $expectedWarningMessage = 'html element should have a lang attribute.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../../fixtures/tests/valid_html_recommended_attribute.html");

        if (false === $html) {
            $this->fail('Failed to read the HTML fixture file.');
        }

        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->getErrors());
        $this->assertNotEmpty($result->getWarnings());
        $this->assertCount(
            1,
            $result->getWarnings()
        );
        $this->assertInstanceOf(
            RecommendedAttributeWarning::class,
            $result->getWarnings()[0],
        );
        $this->assertEquals(
            $expectedWarningMessage,
            $result->getWarnings()[0]->getMessage(),
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

        $this->assertFalse($result->isValid());
        $this->assertNotEmpty($result->getErrors());
        $this->assertMessageExists(
            $result->getErrors(),
            $missingHeadElementMessage
        );
        $this->assertMessageExists(
            $result->getErrors(),
            $missingTitleElementMessage
        );
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

        $this->assertFalse($result->isValid());
        $this->assertNotEmpty($result->getErrors());
        $this->assertMessageExists(
            $result->getErrors(),
            $duplicateTitleElementMessage
        );
        $this->assertMessageExists(
            $result->getErrors(),
            $invalidHeadingElementContentMessage
        );
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

        $this->assertFalse($result->isValid());
        $this->assertNotEmpty($result->getErrors());
        $this->assertNotEmpty($result->getWarnings());
        $this->assertMessageExists(
            $result->getErrors(),
            $duplicateTitleElementMessage
        );
        $this->assertMessageExists(
            $result->getWarnings(),
            $emptyParagraphElementMessage
        );
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

        $this->assertFalse($result->isValid());
        $this->assertNotEmpty($result->getErrors());
        $this->assertMessageExists(
            $result->getErrors(),
            $invalidContentHeadElementMessage
        );
        $this->assertMessageExists(
            $result->getErrors(),
            $invalidContentBodyElementMessage
        );
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

        $this->assertFalse($result->isValid());
        $this->assertNotEmpty($result->getErrors());
        $this->assertMessageExists(
            $result->getErrors(),
            $invalidContentHeadingElementMessage
        );
        $this->assertMessageExists(
            $result->getErrors(),
            $invalidContentParagraphElementMessage
        );
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

        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->getErrors());
        $this->assertNotEmpty($result->getWarnings());
        $this->assertMessageExists(
            $result->getWarnings(),
            $recommendedLangAttributeMessage
        );
        $this->assertMessageExists(
            $result->getWarnings(),
            $emptyParagraphElementMessage
        );
    }
}

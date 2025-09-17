<?php

declare(strict_types= 1);

namespace Niccolo\DocparserPhp\Tests\Model\Core\Validator;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Core\Validator\ValidatorComponent;
use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\Model\Utils\Error\MissingElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\NotUniqueElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\StructuralError;
use Niccolo\DocparserPhp\Model\Utils\Warning\EmptyElementWarning;
use Niccolo\DocparserPhp\Model\Utils\Warning\RecommendedAttributeWarning;

class ValidatorComponentTest extends TestCase
{
    public function test_html_validator_component_valid_html(): void
    {
        $html = file_get_contents(filename: __DIR__ . "/../../../../fixtures/tests/valid_html.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertTrue(condition: $result->isValid());
        $this->assertEmpty(actual: $result->getErrors());
        $this->assertEmpty(actual: $result->getWarnings());
    }

    public function test_html_validator_component_invalid_html_body(): void
    {
        $expectedErrorMessage = 'Invalid tag <meta> detected in body element.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../fixtures/tests/invalid_html_body.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../config/Validator/validator_html.yaml"
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
        $expectedErrorMessage = 'The doctype element is missing or not written properly.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../fixtures/tests/invalid_html_doctype.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../config/Validator/validator_html.yaml"
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
        $html = file_get_contents(filename: __DIR__ . "/../../../../fixtures/tests/invalid_html_heading.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../config/Validator/validator_html.yaml"
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
        $html = file_get_contents(filename: __DIR__ . "/../../../../fixtures/tests/invalid_html_head.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../config/Validator/validator_html.yaml"
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
        $html = file_get_contents(filename: __DIR__ . "/../../../../fixtures/tests/invalid_html_html.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../config/Validator/validator_html.yaml"
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
        $html = file_get_contents(filename: __DIR__ . "/../../../../fixtures/tests/invalid_html_paragraph.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../config/Validator/validator_html.yaml"
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
        $html = file_get_contents(filename: __DIR__ . "/../../../../fixtures/tests/invalid_html_title.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../config/Validator/validator_html.yaml"
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
        $html = file_get_contents(filename: __DIR__ . "/../../../../fixtures/tests/valid_html_empty_element.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../config/Validator/validator_html.yaml"
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
        $html = file_get_contents(filename: __DIR__ . "/../../../../fixtures/tests/valid_html_recommended_attribute.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../config/Validator/validator_html.yaml"
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
}

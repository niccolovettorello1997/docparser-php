<?php

declare(strict_types= 1);

namespace Niccolo\DocparserPhp\Tests\Model\Core\Validator;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Core\Validator\ValidatorComponent;
use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\Model\Utils\Error\MalformedElementError;
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
        $this->assertNull(actual: $result->getError());
        $this->assertEmpty(actual: $result->getWarnings());
    }

    public function test_html_validator_component_invalid_html_body(): void
    {
        $expectedErrorMessage = 'The element \'body\' has invalid content.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../fixtures/tests/invalid_html_body.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotNull(actual: $result->getError());
        $this->assertInstanceOf(
            expected: InvalidContentError::class,
            actual: $result->getError(),
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getError()->getMessage(),
        );
    }

    public function test_html_validator_component_invalid_html_doctype(): void
    {
        $expectedErrorMessage = 'The required element \'doctype\' is missing.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../fixtures/tests/invalid_html_doctype.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotNull(actual: $result->getError());
        $this->assertInstanceOf(
            expected: MissingElementError::class,
            actual: $result->getError(),
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getError()->getMessage(),
        );
    }

    public function test_html_validator_component_invalid_html_heading(): void
    {
        $expectedErrorMessage = 'The element \'heading\' has invalid content.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../fixtures/tests/invalid_html_heading.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotNull(actual: $result->getError());
        $this->assertInstanceOf(
            expected: InvalidContentError::class,
            actual: $result->getError(),
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getError()->getMessage(),
        );
    }

    public function test_html_validator_component_invalid_html_head(): void
    {
        $expectedErrorMessage = 'The element \'head\' has an invalid structure.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../fixtures/tests/invalid_html_head.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotNull(actual: $result->getError());
        $this->assertInstanceOf(
            expected: StructuralError::class,
            actual: $result->getError(),
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getError()->getMessage(),
        );
    }

    public function test_html_validator_component_invalid_html_html(): void
    {
        $expectedErrorMessage = 'The required element \'html\' is missing.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../fixtures/tests/invalid_html_html.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotNull(actual: $result->getError());
        $this->assertInstanceOf(
            expected: MissingElementError::class,
            actual: $result->getError(),
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getError()->getMessage(),
        );
    }

    public function test_html_validator_component_invalid_html_paragraph(): void
    {
        $expectedErrorMessage = 'The element \'paragraph\' has an invalid structure.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../fixtures/tests/invalid_html_paragraph.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotNull(actual: $result->getError());
        $this->assertInstanceOf(
            expected: StructuralError::class,
            actual: $result->getError(),
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getError()->getMessage(),
        );
    }

    public function test_html_validator_component_invalid_html_title(): void
    {
        $expectedErrorMessage = 'The element \'title\' is present multiple times.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../fixtures/tests/invalid_html_title.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotNull(actual: $result->getError());
        $this->assertInstanceOf(
            expected: NotUniqueElementError::class,
            actual: $result->getError(),
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getError()->getMessage(),
        );
    }

    public function test_html_validator_component_valid_html_empty_element(): void
    {
        $expectedErrorMessage = 'The element \'paragraph\' should not be empty.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../fixtures/tests/valid_html_empty_element.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertTrue(condition: $result->isValid());
        $this->assertNull(actual: $result->getError());
        $this->assertNotEmpty(actual: $result->getWarnings());
        $this->assertCount(expectedCount: 1, haystack: $result->getWarnings());
        $this->assertInstanceOf(
            expected: EmptyElementWarning::class,
            actual: $result->getWarnings()[0],
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getWarnings()[0]->getMessage(),
        );
    }

    public function test_html_validator_component_valid_html_recommended_attribute(): void
    {
        $expectedErrorMessage = 'The attribute \'lang\' is recommended.';
        $html = file_get_contents(filename: __DIR__ . "/../../../../fixtures/tests/valid_html_recommended_attribute.html");
        $validator = ValidatorComponent::build(
            context: $html,
            configPath: __DIR__ . "/../../../../config/Validator/validator_html.yaml"
        );

        $result = $validator->run();

        $this->assertTrue(condition: $result->isValid());
        $this->assertNull(actual: $result->getError());
        $this->assertNotEmpty(actual: $result->getWarnings());
        $this->assertCount(expectedCount: 1, haystack: $result->getWarnings());
        $this->assertInstanceOf(
            expected: RecommendedAttributeWarning::class,
            actual: $result->getWarnings()[0],
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getWarnings()[0]->getMessage(),
        );
    }
}

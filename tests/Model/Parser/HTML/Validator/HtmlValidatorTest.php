<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Model\Parser\HTML\Validator;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Utils\Parser\Validator\SharedContext;
use Niccolo\DocparserPhp\Model\Utils\Error\StructuralError;
use Niccolo\DocparserPhp\Model\Utils\Error\MissingElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\MalformedElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\NotUniqueElementError;
use Niccolo\DocparserPhp\Model\Parser\HTML\Validator\HtmlValidator;
use Niccolo\DocparserPhp\Model\Utils\Warning\RecommendedAttributeWarning;

class HtmlValidatorTest extends TestCase
{
    public function test_valid_html_element(): void
    {
        $html = '<!DOCTYPE html><html lang="de"><head><title>Test</title></head><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HtmlValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertTrue(condition: $elementValidationResult->isValid());
        $this->assertNull(actual: $elementValidationResult->getError());
        $this->assertEmpty(actual: $elementValidationResult->getWarnings());
    }

    public function test_missing_html_element(): void
    {
        $expectedErrorMessage = 'The required element \'html\' is missing.';
        $html = '<!DOCTYPE html><head><title>Test</title></head><body><p>Hello, World!</p></body>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HtmlValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertNotNull(actual: $elementValidationResult->getError());
        $this->assertInstanceOf(
            expected: MissingElementError::class,
            actual: $elementValidationResult->getError()
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $elementValidationResult->getError()->getMessage()
        );
    }

    public function test_malformed_html_element(): void
    {
        $expectedErrorMessage = 'The element \'html\' is malformed.';
        $html = '<!DOCTYPE html><html><head><title>Test</title></head><body><p>Hello, World!</p>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HtmlValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertNotNull(actual: $elementValidationResult->getError());
        $this->assertInstanceOf(
            expected: MalformedElementError::class,
            actual: $elementValidationResult->getError()
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $elementValidationResult->getError()->getMessage()
        );
    }

    public function test_multiple_html_elements(): void
    {
        $expectedErrorMessage = 'The element \'html\' is present multiple times.';
        $html = '<!DOCTYPE html><html><head><title>Test</title></head><body><p>Hello, World!</p></body></html><html></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HtmlValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertNotNull(actual: $elementValidationResult->getError());
        $this->assertInstanceOf(
            expected: NotUniqueElementError::class,
            actual: $elementValidationResult->getError()
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $elementValidationResult->getError()->getMessage()
        );
    }

    public function test_html_element_invalid_structure_prefix_and_suffix(): void
    {
        $expectedErrorMessage = 'The element \'html\' has an invalid structure.';
        $html = '<!DOCTYPE html><p><html><head><title>Test</title></head><body><p>Hello, World!</p></body></html></p>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HtmlValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertNotNull(actual: $elementValidationResult->getError());
        $this->assertInstanceOf(
            expected: StructuralError::class,
            actual: $elementValidationResult->getError()
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $elementValidationResult->getError()->getMessage()
        );
    }

    public function test_html_element_missing_head(): void
    {
        $expectedErrorMessage = 'The required element \'head\' is missing.';
        $html = '<!DOCTYPE html><html><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HtmlValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertNotNull(actual: $elementValidationResult->getError());
        $this->assertInstanceOf(
            expected: MissingElementError::class,
            actual: $elementValidationResult->getError()
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $elementValidationResult->getError()->getMessage()
        );
    }

    public function test_html_element_missing_body(): void
    {
        $expectedErrorMessage = 'The required element \'body\' is missing.';
        $html = '<!DOCTYPE html><html><head><title>Test</title></head></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HtmlValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertNotNull(actual: $elementValidationResult->getError());
        $this->assertInstanceOf(
            expected: MissingElementError::class,
            actual: $elementValidationResult->getError()
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $elementValidationResult->getError()->getMessage()
        );
    }

    public function test_html_element_body_before_head(): void
    {
        $expectedErrorMessage = 'The element \'html\' has an invalid structure.';
        $html = '<!DOCTYPE html><html><body><p>Hello, World!</p></body><head><title>Test</title></head></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HtmlValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertNotNull(actual: $elementValidationResult->getError());
        $this->assertInstanceOf(
            expected: StructuralError::class,
            actual: $elementValidationResult->getError()
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $elementValidationResult->getError()->getMessage()
        );
    }

    public function test_html_element_should_have_lang_attribute(): void
    {
        $expectedWarningMessage = 'The attribute \'lang\' is recommended.';
        $html = '<!DOCTYPE html><html><head><title>Test</title></head><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HtmlValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertNotNull(actual: $elementValidationResult->getWarnings());
        $this->assertInstanceOf(
            expected: RecommendedAttributeWarning::class,
            actual: $elementValidationResult->getWarnings()[0]
        );
        $this->assertEquals(
            expected: $expectedWarningMessage,
            actual: $elementValidationResult->getWarnings()[0]->getMessage()
        );
    }
}

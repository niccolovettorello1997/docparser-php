<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Parser\HTML\Validator;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;
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
        $this->assertEmpty(actual: $elementValidationResult->getErrors());
        $this->assertEmpty(actual: $elementValidationResult->getWarnings());
    }

    public function test_missing_html_element(): void
    {
        $expectedErrorMessage = 'The required element html is missing or incorrectly written.';
        $html = '<!DOCTYPE html><head><title>Test</title></head><body><p>Hello, World!</p></body>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HtmlValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertFalse(condition: $elementValidationResult->isValid());
        $this->assertNotNull(actual: $elementValidationResult->getErrors());
        $this->assertInstanceOf(
            expected: MissingElementError::class,
            actual: $elementValidationResult->getErrors()[0]
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $elementValidationResult->getErrors()[0]->getMessage()
        );
    }

    public function test_malformed_html_element(): void
    {
        $expectedErrorMessage = 'The html element is missing a closing tag.';
        $html = '<!DOCTYPE html><html><head><title>Test</title></head><body><p>Hello, World!</p>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HtmlValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertFalse(condition: $elementValidationResult->isValid());
        $this->assertNotEmpty(actual: $elementValidationResult->getErrors());
        $this->assertInstanceOf(
            expected: MalformedElementError::class,
            actual: $elementValidationResult->getErrors()[0]
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $elementValidationResult->getErrors()[0]->getMessage()
        );
    }

    public function test_multiple_html_elements(): void
    {
        $expectedErrorMessage = 'The html element must be unique in the HTML document.';
        $html = '<!DOCTYPE html><html><head><title>Test</title></head><body><p>Hello, World!</p></body></html><html></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HtmlValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertFalse(condition: $elementValidationResult->isValid());
        $this->assertNotEmpty(actual: $elementValidationResult->getErrors());
        $this->assertInstanceOf(
            expected: NotUniqueElementError::class,
            actual: $elementValidationResult->getErrors()[0]
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $elementValidationResult->getErrors()[0]->getMessage()
        );
    }

    public function test_html_element_invalid_structure_prefix_and_suffix(): void
    {
        $expectedErrorMessage = 'Not allowed content before or after the html element.';
        $html = '<!DOCTYPE html><p><html><head><title>Test</title></head><body><p>Hello, World!</p></body></html></p>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HtmlValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertFalse(condition: $elementValidationResult->isValid());
        $this->assertNotEmpty(actual: $elementValidationResult->getErrors());
        $this->assertInstanceOf(
            expected: StructuralError::class,
            actual: $elementValidationResult->getErrors()[0]
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $elementValidationResult->getErrors()[0]->getMessage()
        );
    }

    public function test_html_element_missing_head(): void
    {
        $expectedErrorMessage = 'head element in the html element is missing or incorrectly written.';
        $html = '<!DOCTYPE html><html><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HtmlValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertFalse(condition: $elementValidationResult->isValid());
        $this->assertNotEmpty(actual: $elementValidationResult->getErrors());
        $this->assertInstanceOf(
            expected: MissingElementError::class,
            actual: $elementValidationResult->getErrors()[0]
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $elementValidationResult->getErrors()[0]->getMessage()
        );
    }

    public function test_html_element_missing_body(): void
    {
        $expectedErrorMessage = 'body element in the html element is missing or incorrectly written.';
        $html = '<!DOCTYPE html><html><head><title>Test</title></head></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HtmlValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertFalse(condition: $elementValidationResult->isValid());
        $this->assertNotEmpty(actual: $elementValidationResult->getErrors());
        $this->assertInstanceOf(
            expected: MissingElementError::class,
            actual: $elementValidationResult->getErrors()[0]
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $elementValidationResult->getErrors()[0]->getMessage()
        );
    }

    public function test_html_element_body_before_head(): void
    {
        $expectedErrorMessage = 'The body element must be after the head element in the html element.';
        $html = '<!DOCTYPE html><html><body><p>Hello, World!</p></body><head><title>Test</title></head></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HtmlValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertFalse(condition: $elementValidationResult->isValid());
        $this->assertNotEmpty(actual: $elementValidationResult->getErrors());
        $this->assertInstanceOf(
            expected: StructuralError::class,
            actual: $elementValidationResult->getErrors()[0]
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $elementValidationResult->getErrors()[0]->getMessage()
        );
    }

    public function test_html_element_should_have_lang_attribute(): void
    {
        $expectedWarningMessage = 'html element should have a lang attribute.';
        $html = '<!DOCTYPE html><html><head><title>Test</title></head><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HtmlValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertTrue(condition: $elementValidationResult->isValid());
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

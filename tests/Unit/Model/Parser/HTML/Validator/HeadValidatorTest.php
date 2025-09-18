<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Parser\HTML\Validator;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;
use Niccolo\DocparserPhp\Model\Utils\Error\StructuralError;
use Niccolo\DocparserPhp\Model\Utils\Error\MalformedElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\NotUniqueElementError;
use Niccolo\DocparserPhp\Model\Parser\HTML\Validator\HeadValidator;

class HeadValidatorTest extends TestCase
{
    public function test_valid_head_element(): void
    {
        $html = '<!DOCTYPE html><html lang="de"><head><title>Test</title></head><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertTrue(condition: $elementValidationResult->isValid());
        $this->assertEmpty(actual: $elementValidationResult->getErrors());
        $this->assertEmpty(actual: $elementValidationResult->getWarnings());
    }

    public function test_multiple_head_elements(): void
    {
        $expectedErrorMessage = 'The head element must be unique in the HTML document.';
        $html = '<!DOCTYPE html><html lang="de"><head><title>Test</title></head><head><title>Another Test</title></head><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadValidator(sharedContext: $sharedContext);

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

    public function test_invalid_characters_between_head_and_html(): void
    {
        $expectedErrorMessage = 'Only whitespaces are allowed before the head element and after the html element.';
        $html = '<!DOCTYPE html><html lang="de">Some text before head element<head><title>Test</title></head><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadValidator(sharedContext: $sharedContext);

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

    public function test_head_element_without_closing_tag(): void
    {
        $expectedErrorMessage = 'head element is missing a closing tag.';
        $html = '<!DOCTYPE html><html lang="de"><head><title>Test</title><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadValidator(sharedContext: $sharedContext);

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

    public function test_head_element_with_nested_html_element(): void
    {
        $expectedErrorMessage = 'Nested html element detected in head element.';
        $html = '<!DOCTYPE html><html lang="de"><head><html><title>Test</title></html></head><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadValidator(sharedContext: $sharedContext);

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

    public function test_head_element_with_nested_body_element(): void
    {
        $expectedErrorMessage = 'Nested body element detected in head element.';
        $html = '<!DOCTYPE html><html lang="de"><head><body><p>Hello, World!</p></body></head><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadValidator(sharedContext: $sharedContext);

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
}

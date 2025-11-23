<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Unit\Model\Parser\HTML\Validator;

use DocparserPhp\Model\Parser\HTML\Validator\HeadValidator;
use DocparserPhp\Model\Utils\Error\MalformedElementError;
use DocparserPhp\Model\Utils\Error\NotUniqueElementError;
use DocparserPhp\Model\Utils\Error\StructuralError;
use DocparserPhp\Model\Utils\Parser\SharedContext;
use PHPUnit\Framework\TestCase;

class HeadValidatorTest extends TestCase
{
    public function test_valid_head_element(): void
    {
        $html = '<!DOCTYPE html><html lang="de"><head><title>Test</title></head><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertTrue($elementValidationResult->isValid());
        $this->assertEmpty($elementValidationResult->getErrors());
        $this->assertEmpty($elementValidationResult->getWarnings());
    }

    public function test_multiple_head_elements(): void
    {
        $expectedErrorMessage = 'The head element must be unique in the HTML document.';
        $html = '<!DOCTYPE html><html lang="de"><head><title>Test</title></head><head><title>Another Test</title></head><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertFalse($elementValidationResult->isValid());
        $this->assertNotEmpty($elementValidationResult->getErrors());
        $this->assertInstanceOf(
            NotUniqueElementError::class,
            $elementValidationResult->getErrors()[0]
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $elementValidationResult->getErrors()[0]->getMessage()
        );
    }

    public function test_invalid_characters_between_head_and_html(): void
    {
        $expectedErrorMessage = 'Only whitespaces are allowed before the head element and after the html element.';
        $html = '<!DOCTYPE html><html lang="de">Some text before head element<head><title>Test</title></head><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertFalse($elementValidationResult->isValid());
        $this->assertNotEmpty($elementValidationResult->getErrors());
        $this->assertInstanceOf(
            StructuralError::class,
            $elementValidationResult->getErrors()[0]
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $elementValidationResult->getErrors()[0]->getMessage()
        );
    }

    public function test_head_element_without_closing_tag(): void
    {
        $expectedErrorMessage = 'head element is missing a closing tag.';
        $html = '<!DOCTYPE html><html lang="de"><head><title>Test</title><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertFalse($elementValidationResult->isValid());
        $this->assertNotEmpty($elementValidationResult->getErrors());
        $this->assertCount(
            2,
            $elementValidationResult->getErrors()
        );
        $this->assertInstanceOf(
            MalformedElementError::class,
            $elementValidationResult->getErrors()[1]
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $elementValidationResult->getErrors()[1]->getMessage()
        );
    }

    public function test_head_element_with_nested_html_element(): void
    {
        $expectedErrorMessage = 'Nested html element detected in head element.';
        $html = '<!DOCTYPE html><html lang="de"><head><html><title>Test</title></html></head><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertFalse($elementValidationResult->isValid());
        $this->assertNotEmpty($elementValidationResult->getErrors());
        $this->assertInstanceOf(
            StructuralError::class,
            $elementValidationResult->getErrors()[0]
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $elementValidationResult->getErrors()[0]->getMessage()
        );
    }

    public function test_head_element_with_nested_body_element(): void
    {
        $expectedErrorMessage = 'Nested body element detected in head element.';
        $html = '<!DOCTYPE html><html lang="de"><head><body><p>Hello, World!</p></body></head><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertFalse($elementValidationResult->isValid());
        $this->assertNotEmpty($elementValidationResult->getErrors());
        $this->assertInstanceOf(
            StructuralError::class,
            $elementValidationResult->getErrors()[0]
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $elementValidationResult->getErrors()[0]->getMessage()
        );
    }
}

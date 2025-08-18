<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Model\Parser\HTML\Validator;

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
        $this->assertNull(actual: $elementValidationResult->getError());
        $this->assertEmpty(actual: $elementValidationResult->getWarnings());
    }

    public function test_multiple_head_elements(): void
    {
        $expectedErrorMessage = 'The element \'head\' is present multiple times.';
        $html = '<!DOCTYPE html><html lang="de"><head><title>Test</title></head><head><title>Another Test</title></head><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadValidator(sharedContext: $sharedContext);

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

    public function test_invalid_characters_between_head_and_html(): void
    {
        $expectedErrorMessage = 'The element \'head\' has an invalid structure.';
        $html = '<!DOCTYPE html><html lang="de">Some text before head element<head><title>Test</title></head><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadValidator(sharedContext: $sharedContext);

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

    public function test_head_element_without_closing_tag(): void
    {
        $expectedErrorMessage = 'The element \'head\' is malformed.';
        $html = '<!DOCTYPE html><html lang="de"><head><title>Test</title><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadValidator(sharedContext: $sharedContext);

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

    public function test_head_element_with_nested_html_element(): void
    {
        $expectedErrorMessage = 'The element \'head\' has an invalid structure.';
        $html = '<!DOCTYPE html><html lang="de"><head><html><title>Test</title></html></head><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadValidator(sharedContext: $sharedContext);

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

    public function test_head_element_with_nested_body_element(): void
    {
        $expectedErrorMessage = 'The element \'head\' has an invalid structure.';
        $html = '<!DOCTYPE html><html lang="de"><head><body><p>Hello, World!</p></body></head><body><p>Hello, World!</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadValidator(sharedContext: $sharedContext);

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
}

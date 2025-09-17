<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Model\Parser\HTML\Validator;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;
use Niccolo\DocparserPhp\Model\Utils\Error\EmptyElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\Model\Utils\Error\MissingElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\NotUniqueElementError;
use Niccolo\DocparserPhp\Model\Parser\HTML\Validator\TitleValidator;

class TitleValidatorTest extends TestCase
{
    public function test_valid_title(): void
    {
        $html = '<html><head><title>Valid Title</title></head><body></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new TitleValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertTrue(condition: $elementValidationResult->isValid());
        $this->assertEmpty(actual: $elementValidationResult->getErrors());
        $this->assertEmpty(actual: $elementValidationResult->getWarnings());
    }

    public function test_missing_title(): void
    {
        $expectedErrorMessage = 'The title element is missing or not written properly.';
        $html = '<html><head></head><body></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new TitleValidator(sharedContext: $sharedContext);

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

    public function test_multiple_title_elements(): void
    {
        $expectedErrorMessage = 'The title element must be unique in the HTML document.';
        $html = '<html><head><title>First Title</title><title>Second Title</title></head><body></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new TitleValidator(sharedContext: $sharedContext);

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

    public function test_empty_title(): void
    {
        $expectedErrorMessage = 'The title element must not be empty.';
        $html = '<html><head><title> </title></head><body></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new TitleValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertFalse(condition: $elementValidationResult->isValid());
        $this->assertNotEmpty(actual: $elementValidationResult->getErrors());
        $this->assertInstanceOf(
            expected: EmptyElementError::class,
            actual: $elementValidationResult->getErrors()[0]
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $elementValidationResult->getErrors()[0]->getMessage()
        );
    }

    public function test_invalid_utf8_characters_within_title(): void
    {
        $expectedErrorMessage = 'Invalid UTF-8 characters detected in title element.';
        $html = "<html><head><title>\xC3\x28</title></head><body></body></html>";
        $sharedContext = new SharedContext(context: $html);
        $validator = new TitleValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertFalse(condition: $elementValidationResult->isValid());
        $this->assertNotEmpty(actual: $elementValidationResult->getErrors());
        $this->assertInstanceOf(
            expected: InvalidContentError::class,
            actual: $elementValidationResult->getErrors()[0]
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $elementValidationResult->getErrors()[0]->getMessage()
        );
    }
}

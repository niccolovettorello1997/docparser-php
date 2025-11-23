<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Unit\Model\Parser\HTML\Validator;

use DocparserPhp\Model\Parser\HTML\Validator\TitleValidator;
use DocparserPhp\Model\Utils\Error\EmptyElementError;
use DocparserPhp\Model\Utils\Error\InvalidContentError;
use DocparserPhp\Model\Utils\Error\MissingElementError;
use DocparserPhp\Model\Utils\Error\NotUniqueElementError;
use DocparserPhp\Model\Utils\Parser\SharedContext;
use PHPUnit\Framework\TestCase;

class TitleValidatorTest extends TestCase
{
    public function test_valid_title(): void
    {
        $html = '<html><head><title>Valid Title</title></head><body></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new TitleValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertTrue($elementValidationResult->isValid());
        $this->assertEmpty($elementValidationResult->getErrors());
        $this->assertEmpty($elementValidationResult->getWarnings());
    }

    public function test_missing_title(): void
    {
        $expectedErrorMessage = 'The title element is missing or not written properly.';
        $html = '<html><head></head><body></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new TitleValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertFalse($elementValidationResult->isValid());
        $this->assertNotEmpty($elementValidationResult->getErrors());
        $this->assertInstanceOf(
            MissingElementError::class,
            $elementValidationResult->getErrors()[0]
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $elementValidationResult->getErrors()[0]->getMessage()
        );
    }

    public function test_multiple_title_elements(): void
    {
        $expectedErrorMessage = 'The title element must be unique in the HTML document.';
        $html = '<html><head><title>First Title</title><title>Second Title</title></head><body></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new TitleValidator(sharedContext: $sharedContext);

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

    public function test_empty_title(): void
    {
        $expectedErrorMessage = 'The title element must not be empty.';
        $html = '<html><head><title> </title></head><body></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new TitleValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertFalse($elementValidationResult->isValid());
        $this->assertNotEmpty($elementValidationResult->getErrors());
        $this->assertInstanceOf(
            EmptyElementError::class,
            $elementValidationResult->getErrors()[0]
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $elementValidationResult->getErrors()[0]->getMessage()
        );
    }

    public function test_invalid_utf8_characters_within_title(): void
    {
        $expectedErrorMessage = 'Invalid UTF-8 characters detected in title element.';
        $html = "<html><head><title>\xC3\x28</title></head><body></body></html>";
        $sharedContext = new SharedContext(context: $html);
        $validator = new TitleValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertFalse($elementValidationResult->isValid());
        $this->assertNotEmpty($elementValidationResult->getErrors());
        $this->assertInstanceOf(
            InvalidContentError::class,
            $elementValidationResult->getErrors()[0]
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $elementValidationResult->getErrors()[0]->getMessage()
        );
    }
}

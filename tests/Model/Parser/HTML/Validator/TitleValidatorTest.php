<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Model\Parser\HTML\Validator;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Utils\Parser\Validator\SharedContext;
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
        $this->assertNull(actual: $elementValidationResult->getError());
        $this->assertEmpty(actual: $elementValidationResult->getWarnings());
    }

    public function test_missing_title(): void
    {
        $expectedErrorMessage = 'The required element \'title\' is missing.';
        $html = '<html><head></head><body></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new TitleValidator(sharedContext: $sharedContext);

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

    public function test_multiple_title_elements(): void
    {
        $expectedErrorMessage = 'The element \'title\' is present multiple times.';
        $html = '<html><head><title>First Title</title><title>Second Title</title></head><body></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new TitleValidator(sharedContext: $sharedContext);

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

    public function test_empty_title(): void
    {
        $expectedErrorMessage = 'The element \'title\' must not be empty.';
        $html = '<html><head><title> </title></head><body></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new TitleValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertNotNull(actual: $elementValidationResult->getError());
        $this->assertInstanceOf(
            expected: EmptyElementError::class,
            actual: $elementValidationResult->getError()
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $elementValidationResult->getError()->getMessage()
        );
    }

    public function test_invalid_utf8_characters_within_title(): void
    {
        $expectedErrorMessage = 'The element \'title\' has invalid content.';
        $html = "<html><head><title>\xC3\x28</title></head><body></body></html>";
        $sharedContext = new SharedContext(context: $html);
        $validator = new TitleValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertNotNull(actual: $elementValidationResult->getError());
        $this->assertInstanceOf(
            expected: InvalidContentError::class,
            actual: $elementValidationResult->getError()
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $elementValidationResult->getError()->getMessage()
        );
    }
}

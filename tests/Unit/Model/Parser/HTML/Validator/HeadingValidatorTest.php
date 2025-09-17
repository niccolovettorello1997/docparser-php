<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Parser\HTML\Validator;

use Niccolo\DocparserPhp\Model\Utils\Error\StructuralError;
use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;
use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\Model\Parser\HTML\Validator\HeadingValidator;

class HeadingValidatorTest extends TestCase
{
    public function test_valid_heading_element(): void
    {
        $html = '<DOCTYPE html><html><head></head><body><h1>Valid Heading</h1></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadingValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertTrue(condition: $result->isValid());
        $this->assertEmpty(actual: $result->getErrors());
        $this->assertEmpty(actual: $result->getWarnings());
    }

    public function test_heading_element_opening_and_closing_tags_not_balanced(): void
    {
        $expectedErrorMessage = 'Closing tag for heading element <h2> does not match the last opening tag.';
        $html = '<DOCTYPE html><html><head></head><body><h1>Unbalanced Heading</h2></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadingValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotEmpty(actual: $result->getErrors());
        $this->assertInstanceOf(
            expected: StructuralError::class,
            actual: $result->getErrors()[0]
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getErrors()[0]->getMessage()
        );
    }

    public function test_heading_element_invalid_internal_tag(): void
    {
        $expectedErrorMessage = 'Invalid content inside heading element <h1> : contains <div> tag.';
        $html = '<DOCTYPE html><html><head></head><body><h1>Invalid <div></div></h1></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadingValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotEmpty(actual: $result->getErrors());
        $this->assertInstanceOf(
            expected: InvalidContentError::class,
            actual: $result->getErrors()[0]
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getErrors()[0]->getMessage()
        );
    }

    public function test_heading_element_empty_content(): void
    {
        $expectedErrorMessage = 'Empty content inside heading element <h1>.';
        $html = '<DOCTYPE html><html><head></head><body><h1></h1></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadingValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertFalse(condition: $result->isValid());
        $this->assertNotNull(actual: $result->getErrors());
        $this->assertInstanceOf(
            expected: InvalidContentError::class,
            actual: $result->getErrors()[0]
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getErrors()[0]->getMessage()
        );
    }
}

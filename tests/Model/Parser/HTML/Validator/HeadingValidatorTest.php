<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Model\Parser\HTML\Validator;

use Niccolo\DocparserPhp\Model\Utils\Error\StructuralError;
use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Utils\Parser\Validator\SharedContext;
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
        $this->assertNull(actual: $result->getError());
        $this->assertEmpty(actual: $result->getWarnings());
    }

    public function test_heading_element_opening_and_closing_tags_not_balanced(): void
    {
        $expectedErrorMessage = 'The element \'heading\' has an invalid structure.';
        $html = '<DOCTYPE html><html><head></head><body><h1>Unbalanced Heading</h2></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadingValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertNotNull(actual: $result->getError());
        $this->assertInstanceOf(
            expected: StructuralError::class,
            actual: $result->getError()
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getError()->getMessage()
        );
    }

    public function test_heading_element_invalid_internal_tag(): void
    {
        $expectedErrorMessage = 'The element \'heading\' has invalid content.';
        $html = '<DOCTYPE html><html><head></head><body><h1>Invalid <div></div></h1></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadingValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertNotNull(actual: $result->getError());
        $this->assertInstanceOf(
            expected: InvalidContentError::class,
            actual: $result->getError()
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getError()->getMessage()
        );
    }

    public function test_heading_element_empty_content(): void
    {
        $expectedErrorMessage = 'The element \'heading\' has invalid content.';
        $html = '<DOCTYPE html><html><head></head><body><h1></h1></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new HeadingValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertNotNull(actual: $result->getError());
        $this->assertInstanceOf(
            expected: InvalidContentError::class,
            actual: $result->getError()
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getError()->getMessage()
        );
    }
}

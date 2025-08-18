<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Model\Parser\HTML\Validator;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;
use Niccolo\DocparserPhp\Model\Utils\Error\StructuralError;
use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\Model\Utils\Error\MalformedElementError;
use Niccolo\DocparserPhp\Model\Parser\HTML\Validator\AnchorValidator;

class AnchorValidatorTest extends TestCase
{
    public function test_valid_anchor_element(): void
    {
        $html = '<DOCTYPE html><html><head></head><body><a href="http://www.example.com">example</a></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new AnchorValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertTrue(condition: $result->isValid());
        $this->assertNull(actual: $result->getError());
        $this->assertEmpty(actual: $result->getWarnings());
    }

    public function test_anchor_element_missing_closing_or_opening_tag(): void
    {
        $expectedErrorMessage = 'The element \'anchor\' is malformed.';
        $html = '<DOCTYPE html><html><head></head><body><a href="http://www.example.com">example.com</a><a href="http://www.example2.com">example2.com</body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new AnchorValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertNotNull(actual: $result->getError());
        $this->assertInstanceOf(
            expected: MalformedElementError::class,
            actual: $result->getError()
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getError()->getMessage()
        );
    }

    public function test_anchor_element_nested(): void
    {
        $expectedErrorMessage = 'The element \'anchor\' has an invalid structure.';
        $html = '<DOCTYPE html><html><head></head><body><a href="http://www.example.com">Outer anchor<a href="http://www.example2.com">Inner anchor</a></a></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new AnchorValidator(sharedContext: $sharedContext);

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

    public function test_anchor_element_invalid_href(): void
    {
        $expectedErrorMessage = 'The element \'anchor\' has an invalid structure.';
        $html = '<DOCTYPE html><html><head></head><body><a href=\'\'>Invalid link</a></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new AnchorValidator(sharedContext: $sharedContext);

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

    public function test_anchor_element_empty_content(): void
    {
        $expectedErrorMessage = 'The element \'anchor\' has invalid content.';
        $html = '<DOCTYPE html><html><head></head><body><a href="http://www.example.com"></a></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new AnchorValidator(sharedContext: $sharedContext);

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

    public function test_anchor_element_duplicated_attributes(): void
    {
        $expectedErrorMessage = 'The element \'anchor\' has an invalid structure.';
        $html = '<DOCTYPE html><html><head></head><body><a href="http://www.example.com" href="http://www.example2.com">example.com</a></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new AnchorValidator(sharedContext: $sharedContext);

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
}

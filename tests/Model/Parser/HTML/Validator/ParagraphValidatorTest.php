<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Model\Parser\HTML\Validator;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Utils\Parser\Validator\SharedContext;
use Niccolo\DocparserPhp\Model\Utils\Error\StructuralError;
use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\Model\Utils\Error\MalformedElementError;
use Niccolo\DocparserPhp\Model\Utils\Warning\EmptyElementWarning;
use Niccolo\DocparserPhp\Model\Parser\HTML\Validator\ParagraphValidator;

class ParagraphValidatorTest extends TestCase
{
    public function test_valid_paragraph_element(): void
    {
        $html = '<DOCTYPE html><html><head></head><body><p>Valid paragraph</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new ParagraphValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertTrue(condition: $result->isValid());
        $this->assertNull(actual: $result->getError());
        $this->assertEmpty(actual: $result->getWarnings());
    }

    public function test_paragraph_element_missing_closing_or_opening_tag(): void
    {
        $expectedErrorMessage = 'The element \'paragraph\' is malformed.';
        $html = '<DOCTYPE html><html><head></head><body><p></p><p>Missing closing tag</body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new ParagraphValidator(sharedContext: $sharedContext);

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

    public function test_paragraph_element_nested(): void
    {
        $expectedErrorMessage = 'The element \'paragraph\' has an invalid structure.';
        $html = '<DOCTYPE html><html><head></head><body><p>Outer paragraph <p>Inner paragraph<p>Inner paragraph 2</p></p></p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new ParagraphValidator(sharedContext: $sharedContext);

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

    public function test_paragraph_element_invalid_tags(): void
    {
        $expectedErrorMessage = 'The element \'paragraph\' has invalid content.';
        $html = '<DOCTYPE html><html><head></head><body><p>Valid paragraph <div>Invalid tag inside paragraph</div></p><p>Valid paragraph 2</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new ParagraphValidator(sharedContext: $sharedContext);

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

    public function test_empty_paragraph_element(): void
    {
        $expectedWarningMessage = 'The element \'paragraph\' should not be empty.';
        $html = '<DOCTYPE html><html><head></head><body><p></p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new ParagraphValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertNotEmpty(actual: $result->getWarnings());
        $this->assertCount(
            expectedCount: 1,
            haystack: $result->getWarnings()
        );
        $this->assertInstanceOf(
            expected: EmptyElementWarning::class,
            actual: $result->getWarnings()[0]
        );
        $this->assertEquals(
            expected: $expectedWarningMessage,
            actual: $result->getWarnings()[0]->getMessage()
        );
    }
}

<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Parser\HTML\Validator;

use Niccolo\DocparserPhp\Model\Parser\HTML\Validator\ParagraphValidator;
use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\Model\Utils\Error\MalformedElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\StructuralError;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;
use Niccolo\DocparserPhp\Model\Utils\Warning\EmptyElementWarning;
use PHPUnit\Framework\TestCase;

class ParagraphValidatorTest extends TestCase
{
    public function test_valid_paragraph_element(): void
    {
        $html = '<DOCTYPE html><html><head></head><body><p>Valid paragraph</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new ParagraphValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->getErrors());
        $this->assertEmpty($result->getWarnings());
    }

    public function test_paragraph_element_missing_closing_or_opening_tag(): void
    {
        $expectedErrorMessage = 'Unclosed paragraph element(s) detected.';
        $html = '<DOCTYPE html><html><head></head><body><p></p><p>Missing closing tag</body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new ParagraphValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertFalse($result->isValid());
        $this->assertNotEmpty($result->getErrors());
        $this->assertInstanceOf(
            MalformedElementError::class,
            $result->getErrors()[0]
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $result->getErrors()[0]->getMessage()
        );
    }

    public function test_paragraph_element_nested(): void
    {
        $expectedErrorMessage = 'Nested paragraph elements are not allowed in paragraph element.';
        $html = '<DOCTYPE html><html><head></head><body><p>Outer paragraph <p>Inner paragraph<p>Inner paragraph 2</p></p></p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new ParagraphValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertFalse($result->isValid());
        $this->assertNotEmpty($result->getErrors());
        $this->assertInstanceOf(
            StructuralError::class,
            $result->getErrors()[0]
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $result->getErrors()[0]->getMessage()
        );
    }

    public function test_paragraph_element_invalid_tags(): void
    {
        $expectedErrorMessage = 'Invalid tag <div> found within paragraph element.';
        $html = '<DOCTYPE html><html><head></head><body><p>Valid paragraph <div>Invalid tag inside paragraph</div></p><p>Valid paragraph 2</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new ParagraphValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertFalse($result->isValid());
        $this->assertNotEmpty($result->getErrors());
        $this->assertInstanceOf(
            InvalidContentError::class,
            $result->getErrors()[0]
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $result->getErrors()[0]->getMessage()
        );
    }

    public function test_empty_paragraph_element(): void
    {
        $expectedWarningMessage = 'Empty paragraph element detected.';
        $html = '<DOCTYPE html><html><head></head><body><p></p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new ParagraphValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertTrue($result->isValid());
        $this->assertNotEmpty($result->getWarnings());
        $this->assertCount(
            1,
            $result->getWarnings()
        );
        $this->assertInstanceOf(
            EmptyElementWarning::class,
            $result->getWarnings()[0]
        );
        $this->assertEquals(
            $expectedWarningMessage,
            $result->getWarnings()[0]->getMessage()
        );
    }
}

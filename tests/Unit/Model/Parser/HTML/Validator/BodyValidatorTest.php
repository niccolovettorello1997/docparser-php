<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Parser\HTML\Validator;

use Niccolo\DocparserPhp\Model\Parser\HTML\Validator\BodyValidator;
use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\Model\Utils\Error\MalformedElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\NotUniqueElementError;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;
use Niccolo\DocparserPhp\Model\Utils\Warning\EmptyElementWarning;
use PHPUnit\Framework\TestCase;

class BodyValidatorTest extends TestCase
{
    public function test_valid_body_element(): void
    {
        $html = '<DOCTYPE html><html><head></head><body><p>Valid body content</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new BodyValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->getErrors());
        $this->assertEmpty($result->getWarnings());
    }

    public function test_multiple_body_elements(): void
    {
        $expectedErrorMessage = 'The body element must be unique in the HTML document.';
        $html = '<DOCTYPE html><html><head></head><body><p>First body</p></body><body><p>Second body</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new BodyValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertNotEmpty($result->getErrors());
        $this->assertInstanceOf(
            NotUniqueElementError::class,
            $result->getErrors()[0]
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $result->getErrors()[0]->getMessage()
        );
    }

    public function test_body_element_with_invalid_content(): void
    {
        $expectedErrorMessage = 'Invalid tag <head> detected in body element.';
        $html = '<DOCTYPE html><html><head></head><body><p></head>Invalid content</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new BodyValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

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

    public function test_body_element_with_invalid_attribute(): void
    {
        $expectedErrorMessage = 'Invalid attribute bgcolor detected in body element.';
        $html = '<DOCTYPE html><html><head></head><body bgcolor="red"><p>Invalid attribute</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new BodyValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

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

    public function test_empty_body_element(): void
    {
        $expectedWarningMessage = 'body element should not be empty.';
        $html = '<DOCTYPE html><html><head></head><body></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new BodyValidator(sharedContext: $sharedContext);

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

    public function test_missing_body_element(): void
    {
        $expectedErrorMessage = 'The body element is missing in the HTML document.';
        $html = 'Useless content without body tag';
        $sharedContext = new SharedContext(context: $html);
        $validator = new BodyValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertFalse($result->isValid());
        $this->assertNotEmpty($result->getErrors());
        $this->assertCount(
            1,
            $result->getErrors()
        );
        $this->assertInstanceOf(
            MalformedElementError::class,
            $result->getErrors()[0]
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $result->getErrors()[0]->getMessage()
        );
    }
}

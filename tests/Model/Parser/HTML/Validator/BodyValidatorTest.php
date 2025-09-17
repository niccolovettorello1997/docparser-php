<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Model\Parser\HTML\Validator;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;
use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\Model\Utils\Error\MalformedElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\NotUniqueElementError;
use Niccolo\DocparserPhp\Model\Utils\Warning\EmptyElementWarning;
use Niccolo\DocparserPhp\Model\Parser\HTML\Validator\BodyValidator;

class BodyValidatorTest extends TestCase
{
    public function test_valid_body_element(): void
    {
        $html = '<DOCTYPE html><html><head></head><body><p>Valid body content</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new BodyValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertTrue(condition: $result->isValid());
        $this->assertEmpty(actual: $result->getErrors());
        $this->assertEmpty(actual: $result->getWarnings());
    }

    public function test_multiple_body_elements(): void
    {
        $expectedErrorMessage = 'The body element must be unique in the HTML document.';
        $html = '<DOCTYPE html><html><head></head><body><p>First body</p></body><body><p>Second body</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new BodyValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertNotEmpty(actual: $result->getErrors());
        $this->assertInstanceOf(
            expected: NotUniqueElementError::class,
            actual: $result->getErrors()[0]
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getErrors()[0]->getMessage()
        );
    }

    public function test_body_element_with_invalid_content(): void
    {
        $expectedErrorMessage = 'Invalid tag <head> detected in body element.';
        $html = '<DOCTYPE html><html><head></head><body><p></head>Invalid content</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new BodyValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

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

    public function test_body_element_with_invalid_attribute(): void
    {
        $expectedErrorMessage = 'Invalid attribute bgcolor detected in body element.';
        $html = '<DOCTYPE html><html><head></head><body bgcolor="red"><p>Invalid attribute</p></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new BodyValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertNotEmpty(actual: $result->getErrors());
        $this->assertInstanceOf(
            expected: MalformedElementError::class,
            actual: $result->getErrors()[0]
        );
        $this->assertEquals(
            expected: $expectedErrorMessage,
            actual: $result->getErrors()[0]->getMessage()
        );
    }

    public function test_empty_body_element(): void
    {
        $expectedWarningMessage = 'body element should not be empty.';
        $html = '<DOCTYPE html><html><head></head><body></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new BodyValidator(sharedContext: $sharedContext);

        $result = $validator->validate();

        $this->assertTrue(condition: $result->isValid());
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

<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Model\Parser\HTML\Validator;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Utils\Parser\Validator\SharedContext;
use Niccolo\DocparserPhp\Model\Utils\Error\MissingElementError;
use Niccolo\DocparserPhp\Model\Parser\HTML\Validator\DoctypeValidator;

class DoctypeValidatorTest extends TestCase
{
    public function test_valid_doctype(): void
    {
        $html = '<!DOCTYPE html><html lang="en"><head><title>Test</title></head><body></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new DoctypeValidator(sharedContext: $sharedContext);
        
        $result = $validator->validate();
        
        $this->assertTrue(condition: $result->isValid());
        $this->assertNull(actual: $result->getError());
        $this->assertEmpty(actual: $result->getWarnings());
    }

    public function test_missing_doctype(): void
    {
        $expectedErrorMessage = 'The required element \'doctype\' is missing.';
        $html = '<html lang="en"><head><title>Test</title></head><body></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new DoctypeValidator(sharedContext: $sharedContext);

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

    public function test_doctype_invalid_prefix(): void
    {
        $expectedErrorMessage = 'The required element \'doctype\' is missing.';
        $html = '<p></p><!DOCTYPE html><html lang="de"><head><title>Test</title></head><body></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new DoctypeValidator(sharedContext: $sharedContext);

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
}

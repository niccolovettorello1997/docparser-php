<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Parser\HTML\Validator;

use Niccolo\DocparserPhp\Model\Parser\HTML\Validator\DoctypeValidator;
use Niccolo\DocparserPhp\Model\Utils\Error\MissingElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\StructuralError;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;
use PHPUnit\Framework\TestCase;

class DoctypeValidatorTest extends TestCase
{
    public function test_valid_doctype(): void
    {
        $html = '<!DOCTYPE html><html lang="en"><head><title>Test</title></head><body></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new DoctypeValidator(sharedContext: $sharedContext);
        
        $result = $validator->validate();
        
        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->getErrors());
        $this->assertEmpty($result->getWarnings());
    }

    public function test_missing_doctype(): void
    {
        $expectedErrorMessage = 'The doctype element is missing.';
        $html = '<html lang="en"><head><title>Test</title></head><body></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new DoctypeValidator(sharedContext: $sharedContext);

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

    public function test_doctype_invalid_prefix(): void
    {
        $expectedErrorMessage = 'The doctype element is preceded by invalid content.';
        $html = '<p></p><!DOCTYPE html><html lang="de"><head><title>Test</title></head><body></body></html>';
        $sharedContext = new SharedContext(context: $html);
        $validator = new DoctypeValidator(sharedContext: $sharedContext);

        $elementValidationResult = $validator->validate();

        $this->assertFalse($elementValidationResult->isValid());
        $this->assertNotEmpty($elementValidationResult->getErrors());
        $this->assertInstanceOf(
            StructuralError::class,
            $elementValidationResult->getErrors()[0]
        );
        $this->assertEquals(
            $expectedErrorMessage,
            $elementValidationResult->getErrors()[0]->getMessage()
        );
    }
}

<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Unit\Model\Utils\Error;

use DocparserPhp\Model\Utils\Error\AbstractError;
use DocparserPhp\Model\Utils\Error\StructuralError;
use PHPUnit\Framework\TestCase;

class StructuralErrorTest extends TestCase
{
    public function test_structural_error(): void
    {
        $testMessage = 'test message';

        $error = new StructuralError(message: $testMessage);

        $this->assertInstanceOf(AbstractError::class, $error);
        $this->assertEquals($testMessage, $error->getMessage());
    }

    public function test_structural_error_to_array(): void
    {
        $testMessage = 'test message';

        $expected = ['message' => $testMessage];

        $error = new StructuralError(message: $testMessage);

        $this->assertEquals($expected, $error->toArray());
    }
}

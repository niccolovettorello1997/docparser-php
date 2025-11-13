<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Utils\Error;

use Niccolo\DocparserPhp\Model\Utils\Error\AbstractError;
use Niccolo\DocparserPhp\Model\Utils\Error\InternalError;
use PHPUnit\Framework\TestCase;

class InternalErrorTest extends TestCase
{
    public function test_internal_error(): void
    {
        $testMessage = 'test message';

        $error = new InternalError(message: $testMessage);

        $this->assertInstanceOf(AbstractError::class, $error);
        $this->assertEquals($testMessage, $error->getMessage());
    }

    public function test_internal_error_to_array(): void
    {
        $testMessage = 'test message';

        $expected = ['message' => $testMessage];

        $error = new InternalError(message: $testMessage);

        $this->assertEquals($expected, $error->toArray());
    }
}

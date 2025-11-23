<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Unit\Model\Utils\Error;

use DocparserPhp\Model\Utils\Error\AbstractError;
use DocparserPhp\Model\Utils\Error\InvalidContentError;
use PHPUnit\Framework\TestCase;

class InvalidContentErrorTest extends TestCase
{
    public function test_invalid_content_error(): void
    {
        $testMessage = 'test message';

        $error = new InvalidContentError(message: $testMessage);

        $this->assertInstanceOf(AbstractError::class, $error);
        $this->assertEquals($testMessage, $error->getMessage());
    }

    public function test_invalid_content_error_to_array(): void
    {
        $testMessage = 'test message';

        $expected = ['message' => $testMessage];

        $error = new InvalidContentError(message: $testMessage);

        $this->assertEquals($expected, $error->toArray());
    }
}

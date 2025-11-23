<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Unit\Model\Utils\Error;

use DocparserPhp\Model\Utils\Error\AbstractError;
use DocparserPhp\Model\Utils\Error\MalformedElementError;
use PHPUnit\Framework\TestCase;

class MalformedElementErrorTest extends TestCase
{
    public function test_malformed_element_error(): void
    {
        $testMessage = 'test message';

        $error = new MalformedElementError(message: $testMessage);

        $this->assertInstanceOf(AbstractError::class, $error);
        $this->assertEquals($testMessage, $error->getMessage());
    }

    public function test_malformed_element_error_to_array(): void
    {
        $testMessage = 'test message';

        $expected = ['message' => $testMessage];

        $error = new MalformedElementError(message: $testMessage);

        $this->assertEquals($expected, $error->toArray());
    }
}

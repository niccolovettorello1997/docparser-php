<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Utils\Error;

use Niccolo\DocparserPhp\Model\Utils\Error\AbstractError;
use Niccolo\DocparserPhp\Model\Utils\Error\NotUniqueElementError;
use PHPUnit\Framework\TestCase;

class NotUniqueElementErrorTest extends TestCase
{
    public function test_not_unique_element_error(): void
    {
        $testMessage = 'test message';

        $error = new NotUniqueElementError(message: $testMessage);

        $this->assertInstanceOf(AbstractError::class, $error);
        $this->assertEquals($testMessage, $error->getMessage());
    }

    public function test_not_unique_element_error_to_array(): void
    {
        $testMessage = 'test message';

        $expected = ['message' => $testMessage];

        $error = new NotUniqueElementError(message: $testMessage);

        $this->assertEquals($expected, $error->toArray());
    }
}

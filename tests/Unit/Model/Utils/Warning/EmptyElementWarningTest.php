<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Unit\Model\Utils\Warning;

use DocparserPhp\Model\Utils\Warning\AbstractWarning;
use DocparserPhp\Model\Utils\Warning\EmptyElementWarning;
use PHPUnit\Framework\TestCase;

class EmptyElementWarningTest extends TestCase
{
    public function test_empty_element_warning(): void
    {
        $testMessage = 'test message';

        $warning = new EmptyElementWarning(message: $testMessage);

        $this->assertInstanceOf(AbstractWarning::class, $warning);
        $this->assertEquals($testMessage, $warning->getMessage());
    }

    public function test_empty_element_warning_to_array(): void
    {
        $testMessage = 'test message';

        $expected = ['message' => $testMessage];

        $warning = new EmptyElementWarning(message: $testMessage);

        $this->assertEquals($expected, $warning->toArray());
    }
}

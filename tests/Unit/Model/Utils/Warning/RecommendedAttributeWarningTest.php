<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Utils\Warning;

use Niccolo\DocparserPhp\Model\Utils\Warning\AbstractWarning;
use Niccolo\DocparserPhp\Model\Utils\Warning\RecommendedAttributeWarning;
use PHPUnit\Framework\TestCase;

class RecommendedAttributeWarningTest extends TestCase
{
    public function test_recommended_attribute_warning(): void
    {
        $testMessage = 'test message';

        $warning = new RecommendedAttributeWarning(message: $testMessage);

        $this->assertInstanceOf(AbstractWarning::class, $warning);
        $this->assertEquals($testMessage, $warning->getMessage());
    }

    public function test_recommended_attribute_warning_to_array(): void
    {
        $testMessage = 'test message';

        $expected = ['message' => $testMessage];

        $warning = new RecommendedAttributeWarning(message: $testMessage);

        $this->assertEquals($expected, $warning->toArray());
    }
}

<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Unit\Model\Utils\Parser\Enum;

use DocparserPhp\Model\Utils\Parser\Enum\RenderingType;
use PHPUnit\Framework\TestCase;

class RenderingTypeEnumTest extends TestCase
{
    public function test_input_type_enum(): void
    {
        $this->assertNotEmpty(RenderingType::cases());
    }
}

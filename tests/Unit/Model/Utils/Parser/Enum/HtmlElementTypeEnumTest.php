<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Utils\Parser\Enum;

use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;
use PHPUnit\Framework\TestCase;

class HtmlElementTypeEnumTest extends TestCase
{
    public function test_input_type_enum(): void
    {
        $this->assertNotEmpty(HtmlElementType::cases());
    }
}

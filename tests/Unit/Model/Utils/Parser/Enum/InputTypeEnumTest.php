<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Unit\Model\Utils\Parser\Enum;

use DocparserPhp\Model\Utils\Parser\Enum\InputType;
use PHPUnit\Framework\TestCase;

class InputTypeEnumTest extends TestCase
{
    public function test_input_type_enum(): void
    {
        $this->assertNotEmpty(InputType::cases());
    }
}

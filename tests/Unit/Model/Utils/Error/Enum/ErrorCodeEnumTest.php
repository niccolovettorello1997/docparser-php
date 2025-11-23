<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Unit\Model\Utils\Error\Enum;

use DocparserPhp\Model\Utils\Error\Enum\ErrorCode;
use PHPUnit\Framework\TestCase;

class ErrorCodeEnumTest extends TestCase
{
    public function test_error_code_enum(): void
    {
        $this->assertNotEmpty(ErrorCode::cases());
    }
}

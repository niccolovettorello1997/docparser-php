<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Utils\Error\Enum;

use Niccolo\DocparserPhp\Model\Utils\Error\Enum\ErrorCode;
use PHPUnit\Framework\TestCase;

class ErrorCodeEnumTest extends TestCase
{
    public function test_error_code_enum(): void
    {
        $this->assertNotEmpty(ErrorCode::cases());
    }
}

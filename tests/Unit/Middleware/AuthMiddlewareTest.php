<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Middleware;

use Niccolo\DocparserPhp\Config\Config;
use Niccolo\DocparserPhp\Middleware\AuthMiddleware;
use PHPUnit\Framework\TestCase;

class AuthMiddlewareTest extends TestCase
{
    public function test_auth_middleware_constructor_initialization(): void
    {
        $configMock = $this->createMock(Config::class);

        $authMiddleware = new AuthMiddleware(config: $configMock);

        $this->assertInstanceOf(AuthMiddleware::class, $authMiddleware);
    }

    public function test_auth_middleware_allows_request_when_authorized(): void
    {
        $configMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $configMock->expects($this->once())
            ->method('get')
            ->willReturn(0);

        $authMiddleware = new AuthMiddleware(config: $configMock);

        $this->assertNull($authMiddleware->handle());
    }
}

<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\View;

use Niccolo\DocparserPhp\View\JsonParserView;
use Niccolo\DocparserPhp\View\RenderableInterface;
use PHPUnit\Framework\TestCase;

class RenderableInterfaceTest extends TestCase
{
    public function test_renderable_interface(): void
    {
        $this->assertInstanceOf(RenderableInterface::class, new JsonParserView());
    }
}

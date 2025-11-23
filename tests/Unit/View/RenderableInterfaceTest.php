<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Unit\View;

use DocparserPhp\View\JsonParserView;
use DocparserPhp\View\RenderableInterface;
use PHPUnit\Framework\TestCase;

class RenderableInterfaceTest extends TestCase
{
    public function test_renderable_interface(): void
    {
        $this->assertInstanceOf(RenderableInterface::class, new JsonParserView());
    }
}

<?php

declare(strict_types=1);

namespace DocparserPhp\View;

interface RenderableInterface
{
    /**
     * Render the object.
     *
     * @return string
     */
    public function render(): string;
}

<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Core\Parser;

class Node
{
    public function __construct(
        private readonly string $tagName,
        private readonly ?string $content,
        private array $attributes,
        /** @var Node[] */
        private array $children,
    ) {
    }

    public function getTagName(): string
    {
        return $this->tagName;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function addChild(Node $child): void
    {
        $this->children[] = $child;
    }
}

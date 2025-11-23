<?php

declare(strict_types=1);

namespace DocparserPhp\Model\Core\Parser;

class Node
{
    public function __construct(
        private readonly string $tagName,
        private readonly ?string $content,
        /** @var array<string, string> */
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

    /**
     * @return array<string, string>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return Node[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function addChild(Node $child): void
    {
        $this->children[] = $child;
    }

    /**
     * Convert the node structure into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [];

        // Name
        $result['Name'] = $this->getTagName();

        // Content if present
        if (null !== $this->getContent()) {
            $content = htmlspecialchars(string: $this->getContent());

            $result['Content'] = $content;
        }

        // Attributes if present
        if (!empty($this->getAttributes())) {
            $attributes = [];

            foreach ($this->getAttributes() as $key => $value) {
                $attributes[$key] = $value;
            }

            $result['Attributes'] = $attributes;
        }

        // Children
        if (!empty($this->getChildren())) {
            $children = [];

            foreach ($this->getChildren() as $childNode) {
                $children[] = $childNode->toArray();
            }

            $result['Children'] = $children;
        }

        return $result;
    }
}

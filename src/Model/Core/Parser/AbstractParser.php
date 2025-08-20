<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Core\Parser;

use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;

abstract class AbstractParser
{
    public function __construct(
        protected ?string $elementName = null,
        protected ?string $content = null,
        /** @var AbstractParser[] */
        protected array $children = [],
    ) {
    }

    public function getElementName(): ?string
    {
        return $this->elementName;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return AbstractParser[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param  AbstractParser[] $children
     * @return void
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    /**
     * Parse the corresponding element.
     * 
     * @param  SharedContext $sharedContext
     * @return AbstractParser[]
     */
    abstract public static function parse(SharedContext $sharedContext): array;
}

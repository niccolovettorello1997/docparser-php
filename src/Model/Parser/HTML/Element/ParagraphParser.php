<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Element;

use Niccolo\DocparserPhp\Model\Core\Parser\AbstractParser;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;

class ParagraphParser extends AbstractParser
{
    /**
     * @inheritDoc
     */
    public static function parse(SharedContext $context): array
    {
        /** @var AbstractParser[] */
        $paragraphs = [];

        // Get all paragraphs and their content
        $patternParagraphElement = '/<p\b[^>]*>(.*?)<\/p>/is';

        preg_match_all(
            pattern: $patternParagraphElement,
            subject: $context->getContext(),
            matches: $paragraphElements
        );

        // Create corresponding objects
        foreach($paragraphElements[1] as $paragraphContent) {
            $paragraph = new ParagraphParser(
                elementName: 'paragraph',
                content: $paragraphContent,
            );

            $paragraphs[] = $paragraph;
        }

        return $paragraphs;
    }
}

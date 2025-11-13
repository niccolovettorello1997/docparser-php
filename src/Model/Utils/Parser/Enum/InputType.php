<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Utils\Parser\Enum;

/**
 * @codeCoverageIgnore
 */
enum InputType: string
{
    case HTML = 'html';
    case MARKDOWN = 'markdown';

    /**
     * Get file extension for the language.
     *
     * @param InputType $type
     *
     * @return string
     */
    public static function getExtension(self $type): string
    {
        return self::extensions()[$type->value];
    }

    /**
     * Map language name to file extensions.
     *
     * @return array<string,string>
     */
    private static function extensions(): array
    {
        return [
            self::HTML->value => '.html',
            self::MARKDOWN->value => '.md',
        ];
    }
}

<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Utils\Parser\Enum;

enum ElementType: string
{
    case DOCTYPE = 'doctype';
    case HTML = 'html';
    case HEAD = 'head';
    case BODY = 'body';
    case TITLE = 'title';
    case PARAGRAPH = 'paragraph';
    case HEADING = 'heading';
}

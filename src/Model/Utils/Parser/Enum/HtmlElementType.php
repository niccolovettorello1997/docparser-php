<?php

declare(strict_types=1);

namespace DocparserPhp\Model\Utils\Parser\Enum;

/**
 * @codeCoverageIgnore
 */
enum HtmlElementType: string
{
    case DOCTYPE = 'doctype';
    case HTML = 'html';
    case HEAD = 'head';
    case BODY = 'body';
    case TITLE = 'title';
    case PARAGRAPHS = 'paragraphs';
    case HEADINGS = 'headings';
    case H1 = 'h1';
    case H2 = 'h2';
    case H3 = 'h3';
    case H4 = 'h4';
    case H5 = 'h5';
    case H6 = 'h6';
    case P = 'p';
}

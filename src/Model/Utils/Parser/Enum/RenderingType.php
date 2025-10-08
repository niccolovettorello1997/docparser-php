<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Utils\Parser\Enum;

/**
 * @codeCoverageIgnore
 */
enum RenderingType: string
{
    case HTML = 'html';
    case JSON = 'json';
}

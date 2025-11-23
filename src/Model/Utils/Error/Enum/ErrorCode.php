<?php

declare(strict_types=1);

namespace DocparserPhp\Model\Utils\Error\Enum;

/**
 * @codeCoverageIgnore
 */
enum ErrorCode: string
{
    case NOT_FOUND = 'ERR_NOT_FOUND';
    case NO_AUTH_HEADER = 'ERR_NO_AUTH_HEADER';
    case INVALID_TOKEN = 'ERR_INVALID_TOKEN';
    case MISSING_REQUIRED_FIELD = 'ERR_MISSING_REQUIRED_FIELD';
    case UNSUPPORTED_TYPE = 'ERR_UNSUPPORTED_TYPE';
    case NO_FILE_UPLOADED = 'ERR_NO_FILE_UPLOADED';
    case UPLOAD_ERROR = 'ERR_UPLOAD';
    case INTERNAL_SERVER_ERROR = 'ERR_INTERNAL_SERVER_ERROR';
}

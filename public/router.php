<?php

declare(strict_types=1);

/**
 * If the script requested is found, proceed as usual.
 * If the route requested starts with /api, pass control ti api.php
 * Otherwise return 404.
 */

/** @var string $requested */
$requested = $_SERVER['REQUEST_URI'];

/** @var string $documentRoot */
$documentRoot = $_SERVER['DOCUMENT_ROOT'];

/** @var string $path */
$path = parse_url($requested, PHP_URL_PATH);

$file = realpath($documentRoot . $path);

if ($file && is_file($file)) {
    return false;
}

if (str_starts_with($path, '/api')) {
    require __DIR__ . '/api.php';
    exit(0);
} else {
    http_response_code(404);
    exit(0);
}

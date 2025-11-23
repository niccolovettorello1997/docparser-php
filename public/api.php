<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use DocparserPhp\Config\Config;
use DocparserPhp\Controller\ApiController;
use DocparserPhp\Controller\Responses\BaseResponse;
use DocparserPhp\Controller\Responses\ErrorResponse;
use DocparserPhp\Core\Container;
use DocparserPhp\Middleware\AuthMiddleware;
use DocparserPhp\Model\Utils\Error\Enum\ErrorCode;

/** @var array<string, string> $serverVar */
$serverVar = $_SERVER;

$path = parse_url(url: $serverVar['REQUEST_URI'], component: PHP_URL_PATH);
$method = $serverVar['REQUEST_METHOD'];

// Set response header
header(header: 'Content-Type: application/json');

/** @var Container $container */
$container = require __DIR__ . '/../bootstrap/container.php';

/** @var Config $config */
$config = $container->get(id: Config::class);

/** @var ApiController $controller */
$controller = $container->get(id: ApiController::class);

/** @var AuthMiddleware $authMiddleware */
$authMiddleware = $container->get(id: AuthMiddleware::class);

$response = new ErrorResponse(
    statusCode: 404,
    content: 'Not Found',
    code: ErrorCode::NOT_FOUND->value
);

// Handle docs
if ($path === '/api/v1/openapi.yaml' && $method === 'GET') {
    header(header: 'Content-Type: application/yaml');
    readfile(filename: __DIR__ . '/docs/openapi.yaml');
    exit;
}

if ($path === '/api/v1/docs' && $method === 'GET') {
    header(header: 'Location: /docs/index.html', replace: true, response_code: 302);
    exit;
}

switch (true) {
    case $path === '/api/v1/parse/file' && $method === 'POST':
        $response = $authMiddleware->handle() ?? $controller->parseFile();
        break;
    case $path === '/api/v1/parse/json' && $method === 'POST':
        $response = $authMiddleware->handle() ?? $controller->parseJson();
        break;
    case $path === '/api/v1/health' && $method === 'GET':
        $responseContent = [
            'status' => 'ok',
            'version' => $config->get(key: 'APP_VERSION'),
        ];

        $response = $authMiddleware->handle() ?? new BaseResponse(
            statusCode: 200,
            content: (false !== ($responseHealth = json_encode($responseContent))) ? $responseHealth : 'Error while encoding health response'
        );
        break;
}

/** @var BaseResponse $response */

/** @var string $responseContent */
$responseContent = $response->getContent();

/** @var int $responseStatusCode */
$responseStatusCode = $response->getStatusCode();

http_response_code(response_code: $response->getStatusCode());
echo $responseContent;
exit;

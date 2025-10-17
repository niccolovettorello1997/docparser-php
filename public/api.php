<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Niccolo\DocparserPhp\Model\Utils\Error\Enum\ErrorCode;
use Niccolo\DocparserPhp\Controller\ApiController;
use Niccolo\DocparserPhp\Controller\Responses\BaseResponse;
use Niccolo\DocparserPhp\Service\ParserService;
use Niccolo\DocparserPhp\Service\ValidatorService;
use Niccolo\DocparserPhp\Middleware\AuthMiddleware;
use Dotenv\Dotenv;
use Niccolo\DocparserPhp\Controller\Responses\ErrorResponse;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Parse path and method
$path = parse_url(url: $_SERVER['REQUEST_URI'], component: PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Set response header
header(header: 'Content-Type: application/json');

// TODO: find a way to automatize DI
$validatorService = new ValidatorService();
$parserService = new ParserService();

$controller = new ApiController(
    validatorService: $validatorService,
    parserService: $parserService
);

// TODO: implement auth
$authMiddleware = new AuthMiddleware();

/** @var BaseResponse $response */
$response = new ErrorResponse(
    statusCode: 404,
    content: 'Not Found',
    code: ErrorCode::NOT_FOUND->value
);

switch (true) {
    case $path === '/api/v1/parse/file' && $method === 'POST':
        $response = $authMiddleware->handle() ?? $controller->parseFile();
        break;
    case $path === '/api/v1/parse/json' && $method === 'POST':
        $response = $authMiddleware->handle() ?? $controller->parseJson();
        break;
    case $path === '/api/v1/health' && $method === 'GET':
        $response = $authMiddleware->handle() ?? new Response(
            statusCode: 200,
            content: json_encode(['status' => 'ok'])
        );
        break;
}

http_response_code(response_code: $response->getStatusCode());
echo $response->getContent();
exit;


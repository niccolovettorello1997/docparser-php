<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Niccolo\DocparserPhp\Controller\ApiController;
use Niccolo\DocparserPhp\Controller\Responses\Response;
use Niccolo\DocparserPhp\Service\ParserService;
use Niccolo\DocparserPhp\Service\ValidatorService;

// Get environment variables
// TODO: configure environment file
//$dotenv = Dotenv\Dotenv::createImmutable(paths: __DIR__ . '/../');
//$dotenv->safeLoad();

// Parse path and method
$path = parse_url(url: $_SERVER['REQUEST_URI'], component: PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Set response header
header(header: 'Content-Type: application/json');

// TODO: implement middleware
// Simple routing with optional authentication
//$authMiddleware = new AuthMiddleware();

// Handle authentication if set
//$authMiddleware->handle();

// TODO: find a way to automatize DI
$validatorService = new ValidatorService();
$parserService = new ParserService();

$controller = new ApiController(
    validatorService: $validatorService,
    parserService: $parserService
);

switch (true) {
    case $path === '/api/v1/parse/file' && $method === 'POST':
        $response = $controller->parseFile();
        http_response_code(response_code: $response->getStatusCode());
        echo $response->getContent();
        break;
    case $path === '/api/v1/parse/text' && $method === 'POST':
        echo $controller->parseText();
        break;
    case $path === '/api/v1/parse/json' && $method === 'POST':
        echo $controller->parseJson();
        break;
    case $path === '/api/v1/health' && $method === 'GET':
        $response = new Response(
            statusCode: 200,
            content: ['status' => 'ok']
        );
        http_response_code(response_code: $response->getStatusCode());
        echo $response->getContent();
        break;
    default:
        $response = new Response(
            statusCode: 404,
            content: ['error' => 'Not Found'],
        );
        http_response_code(response_code: $response->getStatusCode());
        echo $response->getContent();
        break;
}

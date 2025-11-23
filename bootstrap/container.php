<?php

declare(strict_types=1);

use DocparserPhp\Core\Container;
use DocparserPhp\Service\ParserService;
use DocparserPhp\Middleware\AuthMiddleware;
use DocparserPhp\Config\Config;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * --- Load environment variables (.env) ---
 */
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

/**
 * --- Create container ---
 */
$container = new Container();

/**
 * --- Register dependencies ---
 */
/** @var array<string, string> $env */
$env = $_ENV;

$container->set(Config::class, fn(): Config => new Config(env: $env));
$container->set(ParserService::class, function(Container $c): ParserService {
    /** @var Config $config */
    $config = $c->get(id: Config::class);

    return new ParserService(config: $config);
});
$container->set(AuthMiddleware::class, function(Container $c): AuthMiddleware {
    /** @var Config $config */
    $config = $c->get(id: Config::class);

    return new AuthMiddleware(config: $config);   
});

return $container;


<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use DI\ContainerBuilder;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use Souboreu\Controller\FileController;
use Souboreu\Controller\IndexController;

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);
$containerBuilder->addDefinitions([
    PhpRenderer::class => fn() => new PhpRenderer(__DIR__ . '/templates'),
    GithubFlavoredMarkdownConverter::class => fn() => new GithubFlavoredMarkdownConverter(),
]);
$container = $containerBuilder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->get('/', [IndexController::class, 'index']);
$app->get('/{path:.+}', [FileController::class, 'show']);

$app->run();

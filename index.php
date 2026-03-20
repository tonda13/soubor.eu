<?php

declare(strict_types=1);

session_start();

require __DIR__ . '/vendor/autoload.php';

use DI\ContainerBuilder;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\PhpRenderer;
use Souboreu\Controller\AdminController;
use Souboreu\Controller\FileController;
use Souboreu\Controller\IndexController;
use Souboreu\Middleware\AdminAuthMiddleware;

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

// Admin — přihlášení (bez auth)
$app->get('/admin/login', [AdminController::class, 'loginForm']);
$app->post('/admin/login', [AdminController::class, 'login']);
$app->get('/admin/logout', [AdminController::class, 'logout']);

// Admin — chráněné routy
$app->group('/admin', function (RouteCollectorProxy $group) {
    $group->get('',          [AdminController::class, 'list']);
    $group->get('/edit',     [AdminController::class, 'editForm']);
    $group->post('/edit',    [AdminController::class, 'save']);
    $group->post('/rename',  [AdminController::class, 'rename']);
    $group->post('/create',         [AdminController::class, 'create']);
    $group->post('/upload',         [AdminController::class, 'upload']);
    $group->post('/upload/confirm', [AdminController::class, 'uploadConfirm']);
    $group->get('/images',           [AdminController::class, 'imageList']);
    $group->post('/images/upload',   [AdminController::class, 'imageUpload']);
    $group->post('/images/delete',   [AdminController::class, 'imageDelete']);
    $group->post('/delete',  [AdminController::class, 'delete']);
})->add(AdminAuthMiddleware::class);

// Veřejné routy
$app->get('/', [IndexController::class, 'index']);
$app->get('/download/{path:.+}', [FileController::class, 'download']);
$app->get('/{path:.+}', [FileController::class, 'show']);

$app->run();

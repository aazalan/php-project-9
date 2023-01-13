<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use DI\Container;
use Slim\Middleware\MethodOverrideMiddleware;
use Model\DataBase\DataBase;
use App\Controllers\UrlController;
use App\Controllers\UrlCheckController;

require __DIR__ . '/../vendor/autoload.php';

//error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
session_start();

// if (!isset($_SESSION['newSession'])) {
//     $_SESSION['newSession'] = true;
//     $connection = new DataBase();
//     $connection->createTables();
// }

$container = new Container();

$container->set('flash', function() {
    return new \Slim\Flash\Messages();
});

$container->set('UrlController', function($c) {
    $view = $c->get("view");
    return new UrlController($view);
});

$app = AppFactory::createFromContainer($container);
$app->add(MethodOverrideMiddleware::class);
$app->addErrorMiddleware(true, true, true);

$app->get('/', UrlController::class . ':start');

$app->get('/urls', UrlController::class . ':index');

$app->get('/urls/{id}', UrlController::class . ':show');

$app->post('/', UrlController::class . ':create');

$app->post('/urls/{url_id}/checks', UrlCheckController::class . ':create');

$app->run();
<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);

$app->get(
    '/', function (Request $request, Response $response) {
        $renderer = new PhpRenderer(__DIR__ . '/../templates');
        return $renderer->render($response, 'index.phtml');
    }
);

// $app->get(
//     '/styles.css', function (Request $request, Response $response) {
        
//         $renderer = new PhpRenderer(__DIR__ . '/../css');
//         return $renderer->render($response, 'styles.css');
//     }
// );

$app->run();
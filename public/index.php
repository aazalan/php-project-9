<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use Model\DataBase\DataBase;
use Carbon\Carbon;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);

$app->get(
    '/', function (Request $request, Response $response) {
        $renderer = new PhpRenderer(__DIR__ . '/../templates');
        return $renderer->render($response, 'index.phtml');
    }
);

$app->get(
    '/urls', function (Request $request, Response $response) {
        $renderer = new PhpRenderer(__DIR__ . '/../templates');
        return $renderer->render($response, 'urls.phtml');
    }
);

$app->get(
    '/urls/{id}', function (Request $request, Response $response, $args) {
        $id = $args['id'];
        $connection = new DataBase();
        $params = $connection->getUrlDataFromBaseId($id);
        $renderer = new PhpRenderer(__DIR__ . '/../templates');
        return $renderer->render($response, 'id.phtml', $params);
    }
);

$app->post(
    '/', function (Request $request, Response $response) {
        $url = $request->getParsedBody()['url'];

        $connection = new DataBase();
        $connection->writeUrlToBase($url['name']);
        $params = $connection->getUrlDataFromBase($url['name']);
        $id = $params['id'];

        return $response
            ->withHeader('Location', '/urls/' . $id)
            ->withStatus(302);
    }
);

$app->run();
<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use DI\Container;
use Slim\Middleware\MethodOverrideMiddleware;
use Model\DataBase\DataBase;
use App\Check;

require __DIR__ . '/../vendor/autoload.php';

session_start();

if (!isset($_SESSION['newSession'])) {
    $_SESSION['newSession'] = true;
    $connection = new DataBase();
    $connection->createTables();
}

$container = new Container();

$container->set('flash', function() {
    return new \Slim\Flash\Messages();
});

$app = AppFactory::createFromContainer($container);
$app->add(MethodOverrideMiddleware::class);
$app->addErrorMiddleware(true, true, true);

$app->get(
    '/', function (Request $request, Response $response) {
        $messages = $this->get('flash')->getMessages();
        $params = ['flash' => $messages];
        $renderer = new PhpRenderer(__DIR__ . '/../templates');
        return $renderer->render($response, 'index.phtml', $params);
    }
);

$app->get(
    '/urls', function (Request $request, Response $response) {
        $connection = new DataBase();
        $urls = $connection->getAllUrls();
        $params = ['urls' => $urls];
        $renderer = new PhpRenderer(__DIR__ . '/../templates');
        return $renderer->render($response, 'urls.phtml', $params);
    }
);

$app->get(
    '/urls/{id}', function (Request $request, Response $response, $args) {
        $id = $args['id'];
        $connection = new DataBase();
        $params = $connection->getUrlDataFromBaseById($id);

        // $check = new Check($params['name']);
        // $info = $check->getFullCheckInformation();
        // print_r($info);

        $checks = $connection->getChecks($id);
        $messages = $this->get('flash')->getMessages();
        $params['flash'] = $messages;
        $params['checks'] = $checks;

        $renderer = new PhpRenderer(__DIR__ . '/../templates');
        return $renderer->render($response, 'id.phtml', $params);
    }
);

$app->post(
    '/', function (Request $request, Response $response) {
        $url = $request->getParsedBody()['url'];
        $validator = new Valitron\Validator(array('website' => $url['name']));
        $validator->rule('url', 'website');

        if ($validator->validate()) {
            $connection = new DataBase();
            $message = $connection->writeUrlToBase($url['name']);
            $urlData = $connection->getUrlDataFromBaseByName($url['name']);
            $id = $urlData['id'];
            $this->get('flash')->addMessage('success', $message);
    
            return $response
                ->withHeader('Location', '/urls/' . $id)
                ->withStatus(302);
        }
        
        $this->get('flash')->addMessage('failed', 'Некорректный URL');
        return $response
                ->withHeader('Location', '/')
                ->withStatus(302);
    }
);

$app->post(
    '/urls/{url_id}/checks', function (Request $request, Response $response, $args) {
        $id = $args['url_id'];
        $connection = new DataBase();
        $url = $connection->getUrlDataFromBaseById($id);
        $check = new Check($url['name']);
        $message = $connection->addCheck($id, $check);
        $this->get('flash')->addMessage('success', $message);

        return $response
                ->withHeader('Location', '/urls/' . $id)
                ->withStatus(302);
    }
);

$app->run();
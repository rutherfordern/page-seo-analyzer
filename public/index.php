<?php

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Exception\GuzzleException;
use Slim\Factory\AppFactory;
use Slim\Middleware\MethodOverrideMiddleware;
use DI\Container;

use PageAnalyzer\Database\DataBase;
use PageAnalyzer\CheckUrl;

$container = new Container();
$container->set('renderer', function () {
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
$container->set('flash', function () {
    return new \Slim\Flash\Messages();
});

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);
$app->add(MethodOverrideMiddleware::class);

session_start();

$router = $app->getRouteCollector()->getRouteParser();

$pdo = DataBase::getInstance();


$app->get('/', function ($request, $response) {
    return $this->get('renderer')->render($response, 'index.phtml');
});


$app->get('/urls', function ($request, $response) use ($pdo) {
    $urls = $pdo->getAllUrl();

    $params = [
        'urls' => $urls
    ];

    return $this->get('renderer')->render($response, 'urls.phtml', $params);
});


$app->post('/urls', function ($request, $response) use ($pdo, $router) {
    $url = $request->getParsedBodyParam('url');

    $validator = new Valitron\Validator(array('website' => $url['name']));
    $validator->rule('url', 'website');
    $errors = $validator->errors();

    if ($validator->validate() && !empty($url['name']))
    {
        $name = $url['name'];

        $saveAndGetFlashMessage = $pdo->saveDataBaseTableUrl($name);

        $urlName = $pdo->getDateUrlByNameFromDB($name);
        $id = $urlName['id'];

        $this->get('flash')->addMessage('success', $saveAndGetFlashMessage);

        $urlShow = $router->urlFor('showUrl', ['id' => $id]);
        return $response->withRedirect($urlShow);
    }

    $params = [
        'url' => $url,
        'errors' => $errors
    ];

    return $this->get('renderer')->render($response, 'index.phtml', $params);
});


$app->get('/urls/{id}', function ($request, $response, array $args) use ($pdo) {
    $flash = $this->get('flash')->getMessages();
    $id = $args['id'];

    $url = $pdo->getDateUrlByIdFromDB($id);
    $urlCheck = $pdo->getCheckUrlShow($id);

    $params = [
        'flash' => $flash,
        'url' => $url,
        'urlCheck' => $urlCheck
    ];

    return $this->get('renderer')->render($response, 'show.phtml', $params);
})->setName('showUrl');


$app->post('/urls/{url_id}/checks', function ($request, $response, array $args) use ($pdo, $router) {
    $id = $args['url_id'];
    $url = $pdo->getDateUrlByIdFromDB($id);

    try {
        $checkUrl = new CheckUrl($url['name']);
        $checkElements = $checkUrl->getAllCheckElements();
    } catch (GuzzleException) {
        $this->get('flash')->addMessage('danger_url', 'Произошла ошибка при проверке, не удалось подключиться');
        $urlShow = $router->urlFor('showUrl', ['id' => $id]);
        return $response->withRedirect($urlShow);
    }

    $saveAndGetFlashMessage = $pdo->saveDataBaseTableUrlChecks($id, $checkElements);

    $this->get('flash')->addMessage('success', $saveAndGetFlashMessage);

    $urlShow = $router->urlFor('showUrl', ['id' => $id]);
    return $response->withRedirect($urlShow);
});

$app->run();
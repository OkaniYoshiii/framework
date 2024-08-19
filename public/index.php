<?php

declare(strict_types=1);

use Framework\Router;
use Framework\Session;

require_once '../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader, ['strict_variables' => true]);

// $exceptionHandler = ExceptionHandler::getInstance();
// $exceptionHandler->start();

$session = Session::getInstance();
$session->start();

if(is_null($session->get('csrf_token'))) $session->set('csrf_token', bin2hex(random_bytes(20)));

$router = Router::getInstance();
$route = $router->getRoute();
$controller = $route->getController();
$controller = $controller::getInstance();
$method = $route->getMethod();

$response = $controller->{$method}();

$session->set('csrf_token', bin2hex(random_bytes(20)));
echo $session->get('csrf_token');

echo $twig->render($response['template'], $response['variables']);
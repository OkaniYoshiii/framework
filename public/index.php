<?php

declare(strict_types=1);

use App\Router;

require_once '../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader, ['strict_variables' => true]);

// $exceptionHandler = ExceptionHandler::getInstance();
// $exceptionHandler->start();

$router = Router::getInstance();
$route = $router->getRoute();
$controller = $route->getController();
$controller = $controller::getInstance();
$method = $route->getMethod();

$controller->{$method}();
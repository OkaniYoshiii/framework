<?php

declare(strict_types=1);

use App\Autoloader;
use App\Entities\Animal;
use App\Entities\Entity;
use App\ExceptionHandler;
use App\FormBuilder;
use App\QueryBuilder;
use App\Router;

require_once '../vendor/autoload.php';
require_once '../src/Autoloader.php';

$autoloader = Autoloader::getInstance();

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader, ['strict_variables' => true]);

// $exceptionHandler = ExceptionHandler::getInstance();
// $exceptionHandler->start();

$router = Router::getInstance();
$route = $router->getRoute();
$controller = $route->getController();
$controller = $controller::getInstance();
$method = $route->getMethod();

$entity = new Animal();

$controller->{$method}();
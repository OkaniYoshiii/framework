<?php

namespace Framework;

use Framework\Router;
use Framework\Session;

class Webapp
{
    public function init()
    {
        $loader = new \Twig\Loader\FilesystemLoader('../templates');
        $twig = new \Twig\Environment($loader, ['strict_variables' => true]);

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

        echo $twig->render($response['template'], $response['variables']);
    }
}
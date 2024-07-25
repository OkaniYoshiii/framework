<?php

declare(strict_types=1);

use App\Autoloader;

require_once '../vendor/autoload.php';
require_once '../src/Autoloader.php';

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader, ['strict_variables' => true]);

$autoloader = Autoloader::getInstance();

echo $twig->render('index.html.twig');
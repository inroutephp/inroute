<?php
/**
 * Optional usage when inroute is installed using composer
 */
require __DIR__ . '/../vendor/autoload.php';

$pathToRouter = __DIR__ . '/router.php';

if (!file_exists($pathToRouter)) {
    die('Run example/build to generate router');
}

$router = require $pathToRouter;

echo $router->dispatch('/base/app/pagename', $_SERVER);

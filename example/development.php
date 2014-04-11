<?php

// Optional usage during development
// Rebuilds application on every page reload

use inroute\InrouteFactory;

$loader = include __DIR__ . "/../vendor/autoload.php";
$loader->add('', __DIR__.'/Application');

$factory = new InrouteFactory();
$factory->addDirs(array(__DIR__ . '/Application'));
$app = eval($factory->generate());

$uri = '/base/app/pagename';

echo $app->dispatch($uri, $_SERVER);

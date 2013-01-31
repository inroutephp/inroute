<?php

// Optional usage during development
// Rebuilds application on every page reload

use iio\inroute\InrouteFactory;

include __DIR__ . "/../vendor/autoload.php";

$factory = new InrouteFactory();
$factory->setDirs(array(__DIR__ . '/Application'));
$app = eval($factory->generate());

// uri injected? (used when testing)
if (!isset($uri)) {
    $uri = '/application/pagename';
}

echo $app->dispatch($uri, $_SERVER);

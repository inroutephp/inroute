<?php

if (!file_exists(__DIR__ . '/app.php')) {
    echo 'run example/build to generate inroute application';
    die();
}

// Optional usage when inroute is installed using composer 
// Requires that the Application classes are autoloaded

use iio\inroute\InrouteFactory;

$loader = include __DIR__ . '/../vendor/autoload.php';
$loader->add('', __DIR__ . '/Application');

$app = include __DIR__ . '/app.php';

// uri injected? (used when testing)
if (!isset($uri)) {
    $uri = '/application/pagename';
}

echo $app->dispatch($uri, $_SERVER);

<?php

if (!file_exists(__DIR__ . '/app.php')) {
    echo "run example/build to generate inroute application";
    die();
}

// Optional usage when inroute is installed using composer 
// Requires that the Application classes are autoloaded

use itbz\inroute\InrouteFactory;

$loader = include __DIR__ . "/../vendor/autoload.php";
$loader->add('', __DIR__.'/Application');

$app = include "app.php";

echo $app->dispatch('/application/pagename', $_SERVER);

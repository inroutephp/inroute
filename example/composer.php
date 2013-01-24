<?php

if (!file_exists(__DIR__ . '/app.php')) {
    echo "run example/build to generate inroute application";
    die();
}

// Optional usage when inroute is installed using composer 
// Requires that the Application classes are autoloaded

use itbz\inroute\InrouteFactory;

include __DIR__ . "/../vendor/autoload.php";

$app = include "app.php";

echo $app->dispatch('/application/pagename', $_SERVER);

<?php

if (!file_exists(__DIR__ . '/app.php')) {
    echo "run example/build to generate inroute application";
    die();
}

// Running inroute with no autoloader
// phar must be included before the application classes

include __DIR__ . "/../inroute.phar";

include __DIR__ . "/Application/Caller.php";
include __DIR__ . "/Application/Container.php";
include __DIR__ . "/Application/Controller.php";

$app = include "app.php";
echo $app->dispatch('/application/pagename', $_SERVER);

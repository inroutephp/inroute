<?php

// Optional usage during development
// Rebuilds application on every page reload

use itbz\inroute\InrouteFactory;

include __DIR__ . "/../vendor/autoload.php";

$factory = new InrouteFactory();
$factory->setDirs(array('Application'));
$app = eval($factory->generate());

echo $app->dispatch('/application/pagename', $_SERVER);

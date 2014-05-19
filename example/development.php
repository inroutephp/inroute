<?php
// DEVELOPMENT STYLE USAGE
// Rebuilds application on every page reload

error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . "/../vendor/autoload.php";

// Generate router
$factory = new \inroute\InrouteFactory;
$factory->addPath(__DIR__);
$router = eval($factory->generate());

// Dispatch application
// echo $router->dispatch('/path/to/page', 'GET');

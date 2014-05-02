<?php
/**
 * Optional usage during development
 *
 * Rebuilds application on every page reload
 */
include __DIR__ . "/../vendor/autoload.php";

$compiler = new \inroute\Compiler;
$compiler->addPath(__DIR__);
$router = eval($compiler->compile());

echo $router->dispatch('/path/to/page', 'GET');

<?php
/**
 * Optional usage during development
 *
 * Rebuilds application on every page reload
 */
include __DIR__ . "/../vendor/autoload.php";

// Det är är ju helt utdaterat...
// Jag vill ändå ha ett enkelt gränssnitt att köra här
    // liknande det som finns nedan
    // det ska inte krävas 15 rader för att sätta upp en development bootstraper...

$factory = new \inroute\InrouteFactory();
$factory->addDirs(array(__DIR__));
$router = eval($factory->generate());

echo $router->dispatch('/base/app/pagename', $_SERVER);

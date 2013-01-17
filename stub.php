<?php

include "vendor/autoload.php";
header('Content-Type: text/plain');

/*
    - skapa Phar som undermodul. Innehåller bland annat Compiler som bygger phar
    - Inroute.phar ska versionshanteras
    - "target"-inställning till json (att användas av phar)
*/

$factory = new \itbz\inroute\InrouteFactory();
$factory->loadJson('inroute.json');
$inroute = eval($factory->generate());

echo $inroute->dispatch('/foo/yeah', $_SERVER);

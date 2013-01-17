<?php

namespace itbz\inroute;
include "vendor/autoload.php";
header('Content-Type: text/plain');

/*
    - skapa Phar som undermodul. Innehåller bland annat Compiler som bygger phar
    - Inroute.phar ska versionshanteras
    - "target"-inställning till json (att användas av phar)
        om target är en katalog ska namnet bli inroute.phar
        om target är ett filnamn så ska det filnamnet användas
*/

$factory = new InrouteFactory();
$factory->loadJson('inroute.json');
$inroute = eval($factory->generate());

echo $inroute->dispatch('/foo/yeah', $_SERVER);

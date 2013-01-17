<?php

include "vendor/autoload.php";
header('Content-Type: text/plain');

/*
    - "target"-inställning till json (att användas av phar)
    - skapa Phar som undermodul. Innehåller bland annat Compiler som bygger phar
    - Inroute.phar ska versionshanteras
*/

$facade = new \itbz\inroute\InrouteFacade();
$facade->loadSettings((array)json_decode(file_get_contents('inroute.json')));
$inroute = eval($facade->generate());

echo $inroute->dispatch('/foo/yeah', $_SERVER);

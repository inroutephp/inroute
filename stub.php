<?php

namespace itbz\inroute;

include "vendor/autoload.php";
header('Content-Type: text/plain');

/*
    - root kan vara ett argument till phar

    - ta bort stödet för json helt och hållet

    - inroute.phar ska versionshanteras
        så att jag kan distribuera den på detta sätt...
        behöver att ett versionsnummer ska sättas på något sätt..
        php inroute.phar -v

    - "target"-inställning till phar
        om target är en katalog ska namnet bli inroute.phar
        om target är ett filnamn så ska det filnamnet användas
*/

$factory = new InrouteFactory();
$factory->loadJson('inroute.json');
$inroute = eval($factory->generate());

echo $inroute->dispatch('/foo/yeah', $_SERVER);

<?php

namespace itbz\inroute;
include "bin/inroute.phar";

die();

include "vendor/autoload.php";
header('Content-Type: text/plain');

/*
    - composer-beroenden ska ha ordentliga versionsnummer. inget dev-master
    - jag måste lära mig om hur sökvägar i phar fungerar.
        och hur jag kan skriva sökvägar så att de fungerar både i och utanför phar
        så att jag kan få bin/inroute att fungera även utanför phar...
    - kanske vill jag att det ska gå att ange sökväg till caller och container
        - inte bara klass-namn...
    - skapa Phar som undermodul. Innehåller bland annat Compiler som bygger phar
        - och Application som är console programet...
    - Inroute.phar ska versionshanteras
    - "target"-inställning till json (att användas av phar)
        om target är en katalog ska namnet bli inroute.phar
        om target är ett filnamn så ska det filnamnet användas
    - sökvägar i inroute.json ska vara beroende av var json finns i filsystemet
        ej var scriptet som läser json är...
*/

$factory = new InrouteFactory();
$factory->loadJson('inroute.json');
$inroute = eval($factory->generate());

echo $inroute->dispatch('/foo/yeah', $_SERVER);

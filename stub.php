<?php

namespace itbz\inroute;

include "vendor/autoload.php";
header('Content-Type: text/plain');

/*
    - kanske vill jag att det ska gå att ange sökväg till caller och container
        - inte bara klass-namn...
    - sökvägar i inroute.json ska vara beroende av var json finns i filsystemet
        ej var scriptet som läser json är...

    - det ska vara möjligt att använda .phar för att endast includera koden..
        istället för att installera med composer..
        eller?

    - inroute.phar ska versionshanteras
        så att jag kan distribuera den på detta sätt...
        behöver att ett versionsnummer ska sättas på något sätt..
        php inroute.phar -v

    - "target"-inställning till json (att användas av phar)
        om target är en katalog ska namnet bli inroute.phar
        om target är ett filnamn så ska det filnamnet användas
*/

$factory = new InrouteFactory();
$factory->loadJson('inroute.json');
$inroute = eval($factory->generate());

echo $inroute->dispatch('/foo/yeah', $_SERVER);

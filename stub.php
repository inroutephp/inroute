<?php

include "vendor/autoload.php";

//*
$app = include "app.php";
echo $app->dispatch('/foo/yeah', $_SERVER);
die();
// */
// 

/*
    För att skapa app:
        ./bin/compile
        php build/inroute.phar build tests/behat/data/ > app.php

    - version sätts i bin/inroute
        måste göras automatiskt när jag bygger nya versioner
        bygg med phing

    - behat katalogen ska bort!!, skapa istället katalog example
    - skapa en testApp under build .. så kan folk se hur example blir en app...

    - fixa med bootstrap så att inga konstiga classmaps måste göras i composer.json

    - inroute.phar ska versionshanteras
        så att jag kan distribuera den på detta sätt...
        behöver att ett versionsnummer ska sättas på något sätt..
        php inroute.phar -v
*/

header('Content-Type: text/plain');

$factory = new \itbz\inroute\InrouteFactory();
$factory->setDirs(array('tests/behat/data'));
$inroute = eval($factory->generate());

echo $inroute->dispatch('/foo/yeah', $_SERVER);

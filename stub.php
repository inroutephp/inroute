<?php

include "vendor/autoload.php";

/*
    inroute facade test misslyckas eftersom jag inte kan ladda in samma fil flera gånger

    lägg till classes som inställning till json
        det borde lösa problemet!!
*/

$facade = new \itbz\inroute\InrouteFacade('inroute.json');
$inroute = eval($facade->generate());

echo $inroute->dispatch('/foo/yeah', $_SERVER);
